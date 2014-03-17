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

/**
 * An abstract base class for implementing codecs.
 */
abstract class AbstractCodec implements CodecInterface
{
    /**
     * Construct a new codec.
     *
     * @param DataTransformInterface $encodeTransform The encode transform to use.
     * @param DataTransformInterface $decodeTransform The decode transform to use.
     */
    public function __construct(
        DataTransformInterface $encodeTransform,
        DataTransformInterface $decodeTransform
    ) {
        $this->encodeTransform = $encodeTransform;
        $this->decodeTransform = $decodeTransform;
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
     * Get the decode transform.
     *
     * @return DataTransformInterface The decode transform.
     */
    public function decodeTransform()
    {
        return $this->decodeTransform;
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
        list($data) = $this->encodeTransform->transform($data, true);

        return $data;
    }

    /**
     * Decode the supplied data.
     *
     * @param string $data The data to decode.
     *
     * @return string                      The decoded data.
     * @throws TransformExceptionInterface If the data cannot be decoded.
     */
    public function decode($data)
    {
        list($data) = $this->decodeTransform->transform($data, true);

        return $data;
    }

    private $encodeTransform;
    private $decodeTransform;
}
