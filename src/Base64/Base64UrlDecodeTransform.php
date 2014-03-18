<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base64;

use Eloquent\Endec\Exception\InvalidEncodedDataException;
use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\DataTransformInterface;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Decodes data using base64url encoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-5
 */
class Base64UrlDecodeTransform extends AbstractDataTransform
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
        $consumedBytes = $this->calculateConsumeBytes($data, $isEnd, 4);
        if (!$consumedBytes) {
            return array('', 0);
        }

        $consumedData = substr($data, 0, $consumedBytes);
        $outputBuffer = base64_decode(
            str_pad(
                strtr($consumedData, '-_', '+/'),
                $consumedBytes % 4,
                '=',
                STR_PAD_RIGHT
            ),
            true
        );
        if (false === $outputBuffer) {
            throw new InvalidEncodedDataException('base64url', $consumedData);
        }

        return array($outputBuffer, $consumedBytes);
    }

    private static $instance;
}
