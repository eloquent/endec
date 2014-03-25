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
use Eloquent\Endec\Transform\DataTransformInterface;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Decodes data using base64 encoding suitable for MIME message bodies.
 *
 * @link https://tools.ietf.org/html/rfc2045#section-6.8
 */
class Base64MimeDecodeTransform extends Base64DecodeTransform
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
            try {
                list($output) = parent::transform(
                    preg_replace('{[^[:alnum:]+/=]+}', '', $data),
                    true
                );
            } catch (TransformExceptionInterface $e) {
                throw new InvalidEncodedDataException('base64mime', $e->data());
            }

            return array($output, strlen($data));
        }

        $chunks = preg_split(
            '{([^[:alnum:]+/=]+)}',
            $data,
            -1,
            PREG_SPLIT_OFFSET_CAPTURE
        );
        $numChunks = count($chunks);

        $buffer = '';
        $output = '';
        $lastFullyConsumed = -1;
        $consumedBytes = 0;
        for ($i = 0; $i < $numChunks; $i ++) {
            $buffer .= $chunks[$i][0];
            list($thisOutput, $consumedBytes) = parent::transform($buffer);
            $output .= $thisOutput;

            if ($consumedBytes === strlen($buffer)) {
                $buffer = '';
                $lastFullyConsumed = $i;
            } else {
                $buffer = substr($buffer, $consumedBytes);
            }
        }

        if ($lastFullyConsumed > -1) {
            if ($lastFullyConsumed < $numChunks - 1) {
                $consumedBytes = $chunks[$lastFullyConsumed + 1][1];
            } else {
                $consumedBytes = $chunks[$lastFullyConsumed][1] +
                    strlen($chunks[$lastFullyConsumed][0]);
            }
        }

        return array($output, $consumedBytes);
    }

    private static $instance;
}
