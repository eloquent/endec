<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Transform;

use Evenement\EventEmitterTrait;
use Exception;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

/**
 * A stream that applies a data transform.
 */
class TransformStream implements TransformStreamInterface
{
    use EventEmitterTrait;

    /**
     * Construct a new data transform stream.
     *
     * @param DataTransformInterface $transform  The data transform to use.
     * @param integer|null           $bufferSize The buffer size in bytes.
     */
    public function __construct(
        DataTransformInterface $transform,
        $bufferSize = null
    ) {
        if (null === $bufferSize) {
            $bufferSize = 8192;
        }

        $this->transform = $transform;
        $this->bufferSize = $bufferSize;

        $this->isClosed = $this->isPaused = $this->isEnding = false;
        $this->buffer = '';
    }

    /**
     * Get the transform.
     *
     * @return DataTransformInterface The transform.
     */
    public function transform()
    {
        return $this->transform;
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

    /**
     * Returns true if this stream is writable.
     *
     * @return boolean True if writable.
     */
    public function isWritable()
    {
        return !$this->isClosed;
    }

    /**
     * Returns true if this stream is readable.
     *
     * @return boolean True if readable.
     */
    public function isReadable()
    {
        return !$this->isClosed;
    }

    /**
     * Write some data to be transformed.
     *
     * @param string $data The data to transform.
     *
     * @return boolean True if this stream is ready for more data.
     */
    public function write($data)
    {
        if ($this->isClosed) {
            return false;
        }

        $this->buffer .= $data;
        $result = $this->transformBuffer();

        return $result && !$this->isPaused;
    }

    /**
     * Transform and finalize any remaining buffered data.
     *
     * @param string|null $data Additional data to transform before finalizing.
     */
    public function end($data = null)
    {
        if ($this->isClosed) {
            return;
        }

        $this->isEnding = $this->isClosed = true;

        if (null !== $data) {
            $this->buffer .= $data;
        }
        $this->transformBuffer();
    }

    /**
     * Close this stream.
     */
    public function close()
    {
        if ($this->isClosed) {
            return;
        }

        $this->doClose();
    }

    /**
     * Pause this stream.
     */
    public function pause()
    {
        $this->isPaused = true;
    }

    /**
     * Resume this stream.
     */
    public function resume()
    {
        $this->isPaused = false;
        $this->transformBuffer();
    }

    /**
     * Pipe the output of this stream to another stream.
     *
     * @param WritableStreamInterface $destination The destination stream.
     * @param array                   $options     A set of options for the piping process.
     *
     * @return WritableStreamInterface The destination stream.
     */
    public function pipe(
        WritableStreamInterface $destination,
        array $options = array()
    ) {
        Util::pipe($this, $destination, $options);

        return $destination;
    }

    /**
     * Transform the internal data buffer.
     *
     * @return boolean True if successful.
     */
    protected function transformBuffer()
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

            try {
                list($outputBuffer, $consumedBytes) =
                    $this->transform->transform($this->buffer, $this->isEnding);
            } catch (Exception $e) {
                $this->emit('error', array($e, $this));

                return false;
            }

            if ($bufferLength === $consumedBytes) {
                $this->buffer = '';
            } else {
                $this->buffer = substr($this->buffer, $consumedBytes);
            }

            $this->emit('data', array($outputBuffer, $this));
        }

        return true;
    }

    /**
     * Perform the actual work of closing this stream.
     */
    protected function doClose()
    {
        $this->isClosed = true;
        $this->isEnding = $this->isPaused = false;
        $this->buffer = '';

        $this->emit('end', array($this));
        $this->emit('close', array($this));
        $this->removeAllListeners();
    }

    private $transform;
    private $bufferSize;
    private $isClosed;
    private $isPaused;
    private $isEnding;
    private $buffer;
}
