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
 * Encodes data using base32 encoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-6
 */
class Base32EncodeTransform extends AbstractBase32EncodeTransform
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
     * Construct a new base32 encode transform.
     */
    public function __construct()
    {
        parent::__construct(
            [
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
                'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
                'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                'Y', 'Z', '2', '3', '4', '5', '6', '7',
            ]
        );
    }

    private static $instance;
}
