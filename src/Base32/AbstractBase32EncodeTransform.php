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
use Exception;

/**
 * An abstract base class for implementing base32 encode transforms.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-6
 */
abstract class AbstractBase32EncodeTransform implements TransformInterface
{
    /**
     * Construct a new base32 encode transform.
     *
     * @param array<integer,string> $alphabet The base32 alphabet to use.
     */
    protected function __construct(array $alphabet)
    {
        $this->alphabet = $alphabet;
    }

    /**
     * Transform the supplied data.
     *
     * This method may transform only part of the supplied data. The return
     * value includes information about how much data was actually consumed. The
     * transform can be forced to consume all data by passing a boolean true as
     * the $isEnd argument.
     *
     * The $context argument will initially be null, but any value assigned to
     * this variable will persist until the stream transformation is complete.
     * It can be used as a place to store state, such as a buffer.
     *
     * It is guaranteed that this method will be called with $isEnd = true once,
     * and only once, at the end of the stream transformation.
     *
     * @param string  $data     The data to transform.
     * @param mixed   &$context An arbitrary context value.
     * @param boolean $isEnd    True if all supplied data must be transformed.
     *
     * @return tuple<string,integer> A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws Exception             If the data cannot be transformed.
     */
    public function transform($data, &$context, $isEnd = false)
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
        return $this->alphabet[($a >> 3)] .
               $this->alphabet[($a & 0x07) << 2] .
               '======';
    }

    private function map2($a, $b)
    {
        return $this->alphabet[($a >> 3)] .
               $this->alphabet[($a & 0x07) << 2 | $b >> 6] .
               $this->alphabet[($b & 0x3e) >> 1] .
               $this->alphabet[($b & 0x01) << 4] .
               '====';
    }

    private function map3($a, $b, $c)
    {
        return $this->alphabet[($a >> 3)] .
               $this->alphabet[($a & 0x07) << 2 | $b >> 6] .
               $this->alphabet[($b & 0x3e) >> 1] .
               $this->alphabet[($b & 0x01) << 4 | $c >> 4] .
               $this->alphabet[($c & 0x0f) << 1] .
               '===';
    }

    private function map4($a, $b, $c, $d)
    {
        return $this->alphabet[($a >> 3)] .
               $this->alphabet[($a & 0x07) << 2 | $b >> 6] .
               $this->alphabet[($b & 0x3e) >> 1] .
               $this->alphabet[($b & 0x01) << 4 | $c >> 4] .
               $this->alphabet[($c & 0x0f) << 1 | $d >> 7] .
               $this->alphabet[($d & 0x7c) >> 2] .
               $this->alphabet[($d & 0x03) << 3] .
               '=';
    }

    private function map5($a, $b, $c, $d, $e)
    {
        return $this->alphabet[($a >> 3)] .
               $this->alphabet[($a & 0x07) << 2 | $b >> 6] .
               $this->alphabet[($b & 0x3e) >> 1] .
               $this->alphabet[($b & 0x01) << 4 | $c >> 4] .
               $this->alphabet[($c & 0x0f) << 1 | $d >> 7] .
               $this->alphabet[($d & 0x7c) >> 2] .
               $this->alphabet[($d & 0x03) << 3 | $e >> 5] .
               $this->alphabet[($e & 0x1f)];
    }

    private $alphabet;
}
