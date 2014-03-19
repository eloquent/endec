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
 * A general-purpose encoder implementation for composing custom encoders.
 */
class Encoder implements EncoderInterface
{
    use EncoderTrait;

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

    private $encodeTransform;
}
