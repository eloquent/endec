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
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Encodes data using base32 encoding.
 */
class Base32EncodeTransform implements DataTransformInterface
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
        $length = strlen($data);
        $consumedBytes = intval($length / 5) * 5;
        $index = 0;
        $output = '';

        while ($index < $consumedBytes) {
            $output .= $this->map5(
                ord($data[$index++]),
                ord($data[$index++]),
                ord($data[$index++]),
                ord($data[$index++]),
                ord($data[$index++])
            );
        }

        if ($isEnd && $consumedBytes !== $length) {
            $remaining = $length - $consumedBytes;
            $consumedBytes = $length;

            if (1 === $remaining) {
                $output .= $this->map1(
                    ord($data[$index])
                );
            } elseif (2 === $remaining) {
                $output .= $this->map2(
                    ord($data[$index++]),
                    ord($data[$index])
                );
            } elseif (3 === $remaining) {
                $output .= $this->map3(
                    ord($data[$index++]),
                    ord($data[$index++]),
                    ord($data[$index])
                );
            } elseif (4 === $remaining) {
                $output .= $this->map4(
                    ord($data[$index++]),
                    ord($data[$index++]),
                    ord($data[$index++]),
                    ord($data[$index])
                );
            }
        }

        return array($output, $consumedBytes);
    }

    private function map1($a)
    {
        return self::$alphabet[($a >> 3)] .
               self::$alphabet[($a & 0x07) << 2] .
               '======';
    }

    private function map2($a, $b)
    {
        return self::$alphabet[($a >> 3)] .
               self::$alphabet[($a & 0x07) << 2 | $b >> 6] .
               self::$alphabet[($b & 0x3e) >> 1] .
               self::$alphabet[($b & 0x01) << 4] .
               '====';
    }

    private function map3($a, $b, $c)
    {
        return self::$alphabet[($a >> 3)] .
               self::$alphabet[($a & 0x07) << 2 | $b >> 6] .
               self::$alphabet[($b & 0x3e) >> 1] .
               self::$alphabet[($b & 0x01) << 4 | $c >> 4] .
               self::$alphabet[($c & 0x0f) << 1] .
               '===';
    }

    private function map4($a, $b, $c, $d)
    {
        return self::$alphabet[($a >> 3)] .
               self::$alphabet[($a & 0x07) << 2 | $b >> 6] .
               self::$alphabet[($b & 0x3e) >> 1] .
               self::$alphabet[($b & 0x01) << 4 | $c >> 4] .
               self::$alphabet[($c & 0x0f) << 1 | $d >> 7] .
               self::$alphabet[($d & 0x7c) >> 2] .
               self::$alphabet[($d & 0x03) << 3] .
               '=';
    }

    private function map5($a, $b, $c, $d, $e)
    {
        return self::$alphabet[($a >> 3)] .
               self::$alphabet[($a & 0x07) << 2 | $b >> 6] .
               self::$alphabet[($b & 0x3e) >> 1] .
               self::$alphabet[($b & 0x01) << 4 | $c >> 4] .
               self::$alphabet[($c & 0x0f) << 1 | $d >> 7] .
               self::$alphabet[($d & 0x7c) >> 2] .
               self::$alphabet[($d & 0x03) << 3 | $e >> 5] .
               self::$alphabet[($e & 0x1f)];
    }

    private static $instance;
    private static $alphabet = array(
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
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
    );
}
