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

use Eloquent\Endec\Transform\DataTransformInterface;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;
use Eloquent\Endec\Transform\TransformStream;
use Eloquent\Endec\Transform\TransformStreamInterface;

/**
 * A general-purpose encoder implementation for composing custom encoders.
 */
class Encoder implements EncoderInterface
{
    /**
     * Construct a new encoder.
     *
     * @param DataTransformInterface $encodeTransform The encode transform to use.
     */
    public function __construct(DataTransformInterface $encodeTransform)
    {
        $this->encodeTransform = $encodeTransform;
    }

    /**
     * Get the encode transform.
     *
     * @return DataTransformInterface The encode transform.
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
     * @return string                      The encoded data.
     * @throws TransformExceptionInterface If the data cannot be encoded.
     */
    public function encode($data)
    {
        list($data) = $this->encodeTransform()
            ->transform($data, $context, true);

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
