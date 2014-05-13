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
 * A general-purpose decoder implementation for composing custom decoders.
 */
class Decoder implements DecoderInterface
{
    /**
     * Construct a new decoder.
     *
     * @param TransformInterface $decodeTransform The decode transform to use.
     */
    public function __construct(TransformInterface $decodeTransform)
    {
        $this->decodeTransform = $decodeTransform;
    }

    /**
     * Get the decode transform.
     *
     * @return TransformInterface The decode transform.
     */
    public function decodeTransform()
    {
        return $this->decodeTransform;
    }

    /**
     * Decode the supplied data.
     *
     * @param string $data The data to decode.
     *
     * @return string                               The decoded data.
     * @throws Exception\EncodingExceptionInterface If the data cannot be decoded.
     */
    public function decode($data)
    {
        list($data, $consumed, $error) = $this->decodeTransform()
            ->transform($data, $context, true);

        if (null !== $error) {
            throw $error;
        }

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

    private $decodeTransform;
}
