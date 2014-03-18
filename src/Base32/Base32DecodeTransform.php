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
use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Decodes data using base32 encoding.
 */
class Base32DecodeTransform extends AbstractDataTransform
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
        $consumedBytes = $this->calculateConsumeBytes($data, $isEnd, 8);
        if (!$consumedBytes) {
            return array('', 0);
        }

        return array(
            $this->decode(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }

    /**
     * Decodes data using base32 encoding.
     *
     * @param string $data The data to decode.
     *
     * @return string                      The decoded data.
     * @throws TransformExceptionInterface If the data cannot be decoded.
     */
    protected function decode($data)
    {
        $binaryData = '';
        foreach (str_split(rtrim($data, '=')) as $byte) {
            if (!array_key_exists($byte, self::$map)) {
                throw new InvalidEncodedDataException('base32', $data);
            }

            $binaryData .= str_pad(
                decbin(self::$map[$byte]),
                5,
                0,
                STR_PAD_LEFT
            );
        }

        while (0 !== strlen($binaryData) % 8) {
            $binaryData = substr($binaryData, 0, strlen($binaryData) - 1);
        }

        $rawData = '';
        foreach (str_split($binaryData, 8) as $chunk) {
            $rawData .= chr(bindec(str_pad($chunk, 8, 0, STR_PAD_RIGHT)));
        }

        return $rawData;
    }

    private static $instance;
    private static $map = array(
        'A' => 0,
        'B' => 1,
        'C' => 2,
        'D' => 3,
        'E' => 4,
        'F' => 5,
        'G' => 6,
        'H' => 7,
        'I' => 8,
        'J' => 9,
        'K' => 10,
        'L' => 11,
        'M' => 12,
        'N' => 13,
        'O' => 14,
        'P' => 15,
        'Q' => 16,
        'R' => 17,
        'S' => 18,
        'T' => 19,
        'U' => 20,
        'V' => 21,
        'W' => 22,
        'X' => 23,
        'Y' => 24,
        'Z' => 25,
        2 => 26,
        3 => 27,
        4 => 28,
        5 => 29,
        6 => 30,
        7 => 31,
    );
}
