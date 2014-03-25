<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base32;

use Eloquent\Endec\Codec;
use Eloquent\Endec\Encoding\CodecInterface;
use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * A codec for the base32hex encoding protocol.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-7
 */
class Base32Hex extends Codec
{
    /**
     * Get the static instance of this codec.
     *
     * @return CodecInterface The codec.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Construct a new base32hex codec.
     *
     * @param DataTransformInterface|null $encodeTransform The encode transform to use.
     * @param DataTransformInterface|null $decodeTransform The decode transform to use.
     */
    public function __construct(
        DataTransformInterface $encodeTransform = null,
        DataTransformInterface $decodeTransform = null
    ) {
        if (null === $encodeTransform) {
            $encodeTransform = Base32HexEncodeTransform::instance();
        }
        if (null === $decodeTransform) {
            $decodeTransform = Base32HexDecodeTransform::instance();
        }

        parent::__construct($encodeTransform, $decodeTransform);
    }

    private static $instance;
}
