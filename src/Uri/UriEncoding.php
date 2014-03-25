<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Uri;

use Eloquent\Endec\Codec;
use Eloquent\Endec\Encoding\CodecInterface;
use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * A codec for the URI percent encoding protocol.
 *
 * @link http://tools.ietf.org/html/rfc3986#section-2.1
 */
class UriEncoding extends Codec
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
     * Construct a new URI encoding codec.
     *
     * @param DataTransformInterface|null $encodeTransform The encode transform to use.
     * @param DataTransformInterface|null $decodeTransform The decode transform to use.
     */
    public function __construct(
        DataTransformInterface $encodeTransform = null,
        DataTransformInterface $decodeTransform = null
    ) {
        if (null === $encodeTransform) {
            $encodeTransform = UriEncodeTransform::instance();
        }
        if (null === $decodeTransform) {
            $decodeTransform = UriDecodeTransform::instance();
        }

        parent::__construct($encodeTransform, $decodeTransform);
    }

    private static $instance;
}
