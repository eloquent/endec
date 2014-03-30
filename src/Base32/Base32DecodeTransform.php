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

use Eloquent\Confetti\TransformInterface;
use Eloquent\Endec\Exception\EncodingExceptionInterface;
use Eloquent\Endec\Exception\InvalidEncodedDataException;

/**
 * Decodes data using base32 encoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-6
 */
class Base32DecodeTransform extends AbstractBase32DecodeTransform
{
    /**
     * Get the static instance of this transform.
     *
     * @return TransformInterface The transform.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Map a byte to its relevant alphabet entry.
     *
     * @param string  $data  The data to be decoded.
     * @param integer $index The index into the data at which the relevant byte is located.
     *
     * @return integer                    The relevant alphabet entry.
     * @throws EncodingExceptionInterface If there is no relevant alphabet entry.
     */
    protected function mapByte($data, $index)
    {
        $byte = ord($data[$index]);
        if ($byte > 49) {
            if ($byte < 56) {
                return $byte - 24;
            }
            if ($byte > 64 && $byte < 91) {
                return $byte - 65;
            }
        }

        throw new InvalidEncodedDataException($this->key(), $data);
    }

    /**
     * Get the string key used to identify this encoding.
     *
     * @return string The key.
     */
    protected function key()
    {
        return 'base32';
    }

    private static $instance;
}
