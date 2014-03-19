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
 * A general-purpose decoder implementation for composing custom decoders.
 */
class Decoder implements DecoderInterface
{
    use DecoderTrait;

    /**
     * Construct a new decoder.
     *
     * @param DataTransformInterface $decodeTransform The decode transform to use.
     */
    public function __construct(DataTransformInterface $decodeTransform)
    {
        $this->decodeTransform = $decodeTransform;
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

    private $decodeTransform;
}
