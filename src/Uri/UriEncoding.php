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

use Eloquent\Confetti\TransformInterface;
use Eloquent\Endec\Codec;
use Eloquent\Endec\Encoding\CodecInterface;

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
     * @param TransformInterface|null $encodeTransform The encode transform to use.
     * @param TransformInterface|null $decodeTransform The decode transform to use.
     */
    public function __construct(
        TransformInterface $encodeTransform = null,
        TransformInterface $decodeTransform = null
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
