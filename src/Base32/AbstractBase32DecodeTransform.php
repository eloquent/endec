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
 * An abstract base class for implementing base32 decode transforms.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-6
 */
abstract class AbstractBase32DecodeTransform implements TransformInterface
{
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
     * @return tuple<string,integer,mixed> A 3-tuple of the transformed data, the number of bytes consumed, and any resulting error.
     */
    public function transform($data, &$context, $isEnd = false)
    {
        $paddedLength = strlen($data);
        $data = rtrim($data, '=');
        $length = strlen($data);
        $consumed = intval($length / 8) * 8;
        $index = 0;
        $output = '';

        try {
            while ($index < $consumed) {
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

            if (($isEnd || $paddedLength > $length) && $consumed !== $length) {
                $remaining = $length - $consumed;
                $consumed = $length;

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
                    return array(
                        '',
                        0,
                        new InvalidEncodedDataException($this->key(), $data)
                    );
                }
            }
        } catch (EncodingExceptionInterface $error) {
            return array('', 0, $error);
        }

        return array($output, $consumed + $paddedLength - $length, null);
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
     * @return integer                    The relevant alphabet entry.
     * @throws EncodingExceptionInterface If there is no relevant alphabet entry.
     */
    abstract protected function mapByte($data, $index);

    /**
     * Get the string key used to identify this encoding.
     *
     * @return string The key.
     */
    abstract protected function key();
}
