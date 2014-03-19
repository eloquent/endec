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
 * A trait for implementing decoders.
 */
trait DecoderTrait
{
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
        list($data) = $this->decodeTransform()->transform($data, true);

        return $data;
    }

    /**
     * Create a new decode stream.
     *
     * @param integer|null $bufferSize The buffer size in bytes.
     *
     * @return TransformStreamInterface The newly created decode stream.
     */
    public function createDecodeStream($bufferSize = null)
    {
        return new TransformStream($this->decodeTransform(), $bufferSize);
    }

    /**
     * Get the decode transform.
     *
     * @return DataTransformInterface The decode transform.
     */
    abstract public function decodeTransform();
}
