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

use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Encodes data using base64 encoding suitable for MIME message bodies.
 *
 * @link https://tools.ietf.org/html/rfc2045#section-6.8
 */
class Base64MimeEncodeTransform extends AbstractDataTransform
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
     * @return tuple<string,integer>                 A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
     */
    public function transform($data, &$context, $isEnd = false)
    {
        if (null === $context) {
            $context = '';
        }
        $context .= $data;

        $output = '';
        $consumedBytes = $this->calculateConsumeBytes($context, $isEnd, 57);
        if ($consumedBytes) {
            $output = chunk_split(
                base64_encode(substr($context, 0, $consumedBytes))
            );

            if (strlen($context) === $consumedBytes) {
                $context = '';
            } else {
                $context = substr($context, $consumedBytes);
            }
        }

        return array($output, strlen($data));
    }

    private static $instance;
}
