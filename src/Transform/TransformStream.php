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
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

/**
 * A stream wrapper for data transforms.
 */
class TransformStream implements TransformStreamInterface
{
    use EventEmitterTrait;

    /**
     * Construct a new data transform.
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
     * Returns true if this transform is writable.
     *
     * @return boolean True if writable.
     */
    public function isWritable()
    {
        return !$this->isClosed;
    }

    /**
     * Returns true if this transform is readable.
     *
     * @return boolean True if readable.
     */
    public function isReadable()
    {
        return !$this->isClosed;
    }

    /**
     * Write some data to this transform.
     *
     * @param string $data The data to transform.
     *
     * @return boolean                               True if this transform is ready for more data.
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
     */
    public function write($data)
    {
        if ($this->isClosed) {
            return false;
        }

        $this->buffer .= $data;
        $this->transformBuffer();

        return !$this->isPaused;
    }

    /**
     * Transform and finalize any remaining buffered data.
     *
     * @param string|null $data Additional data to transform before finalizing.
     *
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
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
     * Close this transform.
     */
    public function close()
    {
        if ($this->isClosed) {
            return;
        }

        $this->doClose();
    }

    /**
     * Pause this transform.
     */
    public function pause()
    {
        $this->isPaused = true;
    }

    /**
     * Resume this transform.
     *
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
     */
    public function resume()
    {
        $this->isPaused = false;
        $this->transformBuffer();
    }

    /**
     * Pipe the output of this transform to another stream.
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
     * This method abstracts some common implementation details so that the
     * concrete data transforms can be simplified.
     *
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
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

            list($outputBuffer, $consumedBytes) =
                $this->transform->transform($this->buffer, $this->isEnding);

            if ($bufferLength === $consumedBytes) {
                $this->buffer = '';
            } else {
                $this->buffer = substr($this->buffer, $consumedBytes);
            }

            $this->emit('data', array($outputBuffer, $this));
        }
    }

    /**
     * Perform the actual work of closing this transform.
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

    /**
     * Calculate the number of bytes to consume based on the encoding's block
     * size and the size of the available data.
     *
     * @param string  $data      The data to consume.
     * @param boolean $isEnd     True if all data should be consumed.
     * @param integer $chunkSize The encoding chunk size.
     *
     * @return integer The amount of data to consume in bytes.
     */
    protected function calculateConsumedBytes($data, $isEnd, $chunkSize)
    {
        $length = strlen($data);
        if ($isEnd) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % $chunkSize);
        }

        return $consumedBytes;
    }

    private $transform;
    private $bufferSize;
    private $isClosed;
    private $isPaused;
    private $isEnding;
    private $buffer;
}
