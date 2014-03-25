<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Uri;

use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\DataTransformInterface;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Decodes data using URI percent encoding.
 *
 * @link http://tools.ietf.org/html/rfc3986#section-2.1
 */
class UriDecodeTransform extends AbstractDataTransform
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
        if ($isEnd) {
            $consumedBytes = strlen($data);
        } else {
            $lastPercentIndex = strrpos($data, '%');

            if (false === $lastPercentIndex) {
                $consumedBytes = strlen($data);
            } else {
                $length = strlen($data);

                if ($lastPercentIndex < $length - 2) {
                    $consumedBytes = $length;
                } else {
                    $consumedBytes = $lastPercentIndex;
                }
            }
        }
        if (!$consumedBytes) {
            return array('', 0);
        }

        return array(
            rawurldecode(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }

    private static $instance;
}
