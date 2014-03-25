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

use Eloquent\Endec\Exception\InvalidEncodedDataException;
use Eloquent\Endec\Transform\DataTransformInterface;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * An abstract base class for implementing base32 decode transforms.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-6
 */
abstract class AbstractBase32DecodeTransform implements DataTransformInterface
{
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
        $paddedLength = strlen($data);
        $data = rtrim($data, '=');
        $length = strlen($data);
        $consumedBytes = intval($length / 8) * 8;
        $index = 0;
        $output = '';

        while ($index < $consumedBytes) {
            $output .= $this->map8(
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++),
                $this->mapByte($data, $index++)
            );
        }

        if (($isEnd || $paddedLength > $length) && $consumedBytes !== $length) {
            $remaining = $length - $consumedBytes;
            $consumedBytes = $length;

            if (2 === $remaining) {
                $output .= $this->map2(
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index)
                );
            } elseif (4 === $remaining) {
                $output .= $this->map4(
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index)
                );
            } elseif (5 === $remaining) {
                $output .= $this->map5(
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index)
                );
            } elseif (7 === $remaining) {
                $output .= $this->map7(
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index++),
                    $this->mapByte($data, $index)
                );
            } else {
                throw new InvalidEncodedDataException($this->key(), $data);
            }
        }

        return array($output, $consumedBytes + $paddedLength - $length);
    }

    private function map2($a, $b)
    {
        return chr($a << 3 | $b >> 2);
    }

    private function map4($a, $b, $c, $d)
    {
        return chr($a << 3 | $b >> 2) .
               chr($b << 6 | $c << 1 | $d >> 4);
    }

    private function map5($a, $b, $c, $d, $e)
    {
        return chr($a << 3 | $b >> 2) .
               chr($b << 6 | $c << 1 | $d >> 4) .
               chr($d << 4 | $e >> 1);
    }

    private function map7($a, $b, $c, $d, $e, $f, $g)
    {
        return chr($a << 3 | $b >> 2) .
               chr($b << 6 | $c << 1 | $d >> 4) .
               chr($d << 4 | $e >> 1) .
               chr($e << 7 | $f << 2 | $g >> 3);
    }

    private function map8($a, $b, $c, $d, $e, $f, $g, $h)
    {
        return chr($a << 3 | $b >> 2) .
               chr($b << 6 | $c << 1 | $d >> 4) .
               chr($d << 4 | $e >> 1) .
               chr($e << 7 | $f << 2 | $g >> 3) .
               chr($g << 5 | $h);
    }

    /**
     * Map a byte to its relevant alphabet entry.
     *
     * @param string  $data  The data to be decoded.
     * @param integer $index The index into the data at which the relevant byte is located.
     *
     * @return integer                     The relevant alphabet entry.
     * @throws TransformExceptionInterface If there is no relevant alphabet entry.
     */
    abstract protected function mapByte($data, $index);

    /**
     * Get the string key used to identify this encoding.
     *
     * @return string The key.
     */
    abstract protected function key();
}
