<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec;

use Eloquent\Confetti\TransformInterface;
use Eloquent\Confetti\TransformStream;
use Eloquent\Confetti\TransformStreamInterface;

/**
 * A general-purpose encoder implementation for composing custom encoders.
 */
class Encoder implements EncoderInterface
{
    /**
     * Construct a new encoder.
     *
     * @param TransformInterface $encodeTransform The encode transform to use.
     */
    public function __construct(TransformInterface $encodeTransform)
    {
        $this->encodeTransform = $encodeTransform;
    }

    /**
     * Get the encode transform.
     *
     * @return TransformInterface The encode transform.
     */
    public function encodeTransform()
    {
        return $this->encodeTransform;
    }

    /**
     * Encode the supplied data.
     *
     * @param string $data The data to encode.
     *
     * @return string                               The encoded data.
     * @throws Exception\EncodingExceptionInterface If the data cannot be encoded.
     */
    public function encode($data)
    {
        list($data, $consumed, $error) = $this->encodeTransform()
            ->transform($data, $context, true);

        if (null !== $error) {
            throw $error;
        }

        return $data;
    }

    /**
     * Create a new encode stream.
     *
     * @param integer|null $bufferSize The buffer size in bytes.
     *
     * @return TransformStreamInterface The newly created encode stream.
     */
    public function createEncodeStream($bufferSize = null)
    {
        return new TransformStream($this->encodeTransform(), $bufferSize);
    }

    private $encodeTransform;
}
