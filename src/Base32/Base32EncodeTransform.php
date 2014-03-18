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

use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Encodes data using base32 encoding.
 */
class Base32EncodeTransform extends AbstractDataTransform
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
     * Transform the supplied data.
     *
     * This method may transform only part of the supplied data. The return
     * value includes information about how much data was actually consumed. The
     * transform can be forced to consume all data by passing a boolean true as
     * the second argument.
     *
     * @param string  $data  The data to transform.
     * @param boolean $isEnd True if all supplied data must be transformed.
     *
     * @return tuple<string,integer>       A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws TransformExceptionInterface If the data cannot be transformed.
     */
    public function transform($data, $isEnd = false)
    {
        $consumedBytes = $this->calculateConsumeBytes($data, $isEnd, 5);
        if (!$consumedBytes) {
            return array('', 0);
        }

        return array(
            $this->encode(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }

    /**
     * Encodes data using base32 encoding.
     *
     * @param string $data The data to encode.
     *
     * @return string The encoded data.
     */
    protected function encode($data)
    {
        $binary = '';
        foreach (str_split($data) as $byte) {
            $binary .= str_pad(decbin(ord($byte)), 8, 0, STR_PAD_LEFT);
        }

        $chunks = str_split($binary, 5);
        while (0 !== count($chunks) % 8) {
            $chunks[] = null;
        }

        $base32 = '';
        foreach ($chunks as $chunk) {
            if (null === $chunk) {
                $base32 .= '=';
            } else {
                $base32 .= self::$map[
                    bindec(str_pad($chunk, 5, 0, STR_PAD_RIGHT))
                ];
            }
        }

        return $base32;
    }

    private static $instance;
    private static $map = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        2,
        3,
        4,
        5,
        6,
        7,
    );
}
