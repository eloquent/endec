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

/**
 * A general-purpose codec implementation for composing custom codecs.
 */
class Codec implements CodecInterface
{
    use EncoderTrait;
    use DecoderTrait;

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

    private $encodeTransform;
    private $decodeTransform;
}
