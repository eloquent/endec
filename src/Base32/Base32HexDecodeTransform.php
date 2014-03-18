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

use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * Decodes data using base32hex encoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-7
 */
class Base32HexDecodeTransform extends AbstractBase32DecodeTransform
{
    /**
     * Get the static instance of this transform.
     *
     * @return DataTransformInterface The transform.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Construct a new base32 decode transform.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                '0' => 0,
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5,
                '6' => 6,
                '7' => 7,
                '8' => 8,
                '9' => 9,
                'A' => 10,
                'B' => 11,
                'C' => 12,
                'D' => 13,
                'E' => 14,
                'F' => 15,
                'G' => 16,
                'H' => 17,
                'I' => 18,
                'J' => 19,
                'K' => 20,
                'L' => 21,
                'M' => 22,
                'N' => 23,
                'O' => 24,
                'P' => 25,
                'Q' => 26,
                'R' => 27,
                'S' => 28,
                'T' => 29,
                'U' => 30,
                'V' => 31,
            )
        );
    }

    private static $instance;
}
