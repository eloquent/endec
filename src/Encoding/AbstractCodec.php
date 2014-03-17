<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Encoding;

use Evenement\EventEmitterTrait;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

/**
 * An abstract base class for implementing codecs.
 */
abstract class AbstractCodec implements CodecInterface
{
    use EventEmitterTrait;

    public function __construct($bufferSize = null)
    {
        if (null === $bufferSize) {
            $bufferSize = 8192;
        }

        $this->bufferSize = $bufferSize;

        $this->isClosed = $this->isPaused = $this->isEnding = false;
        $this->buffer = '';
    }

    /**
     * Get the buffer size.
     *
     * @return integer The buffer size in bytes.
     */
    public function bufferSize()
    {
        return $this->bufferSize;
    }

    public function isWritable()
    {
        return !$this->isClosed;
    }

    public function isReadable()
    {
        return !$this->isClosed;
    }

    public function write($data)
    {
        if ($this->isClosed) {
            return false;
        }

        $this->buffer .= $data;
        $this->processBuffer();

        return !$this->isPaused;
    }

    public function end($data = null)
    {
        if ($this->isClosed) {
            return;
        }

        $this->isEnding = $this->isClosed = true;

        if (null !== $data) {
            $this->buffer .= $data;
        }
        $this->processBuffer();
    }

    public function close()
    {
        if ($this->isClosed) {
            return;
        }

        $this->doClose();
    }

    public function pause()
    {
        $this->isPaused = true;
    }

    public function resume()
    {
        $this->isPaused = false;
        $this->processBuffer();
    }

    public function pipe(
        WritableStreamInterface $destination,
        array $options = array()
    ) {
        Util::pipe($this, $dest, $options);

        return $dest;
    }

    protected function processBuffer()
    {
        while (true) {
            $bufferLength = strlen($this->buffer);
            if (!$bufferLength) {
                if ($this->isEnding) {
                    $this->doClose();
                }

                break;
            }

            if ($this->isPaused) {
                break;
            }

            if (!$this->isEnding && $bufferLength < $this->bufferSize) {
                break;
            }

            list($outputBuffer, $consumedBytes) =
                $this->processInput($this->buffer, $this->isEnding);

            if ($bufferLength === $consumedBytes) {
                $this->buffer = '';
            } else {
                $this->buffer = substr($this->buffer, $consumedBytes);
            }

            $this->emit('data', array($outputBuffer, $this));
        }
    }

    protected function doClose()
    {
        $this->isClosed = true;
        $this->isEnding = false;

        $this->emit('end', array($this));
        $this->emit('close', array($this));
        $this->removeAllListeners();
    }

    protected function calculateConsumedBytes($data, $isEnding, $chunkSize)
    {
        $length = strlen($data);
        if ($isEnding) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % $chunkSize);
        }

        return $consumedBytes;
    }

    abstract protected function processInput($data, $isEnding);

    protected $bufferSize;
    protected $isClosed;
    protected $isPaused;
    protected $isEnding;
    protected $buffer;
}
