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
        $chunks = preg_split(
            '{([^[:alnum:]+/=]+)}',
            $data,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );
        $numChunks = count($chunks);

        $buffer = '';
        $output = '';
        $consumedBytes = 0;
        $lastFullyConsumed = -1;
        for ($i = 0; $i < $numChunks; $i += 2) {
            $buffer .= $chunks[$i];

            try {
                list($thisOutput, $thisConsumedBytes) = parent::transform(
                    $buffer,
                    $isEnd && $numChunks - 1 === $i
                );
            } catch (TransformExceptionInterface $e) {
                throw new InvalidEncodedDataException('base64mime', $e->data());
            }

            $output .= $thisOutput;
            $consumedBytes += $thisConsumedBytes;

            if ($thisConsumedBytes === strlen($buffer)) {
                $buffer = '';
                $lastFullyConsumed = $i;
            } else {
                $buffer = substr($buffer, $thisConsumedBytes);
            }
        }

        for ($i = 1; $i < $lastFullyConsumed + 2 && $i < $numChunks; $i += 2) {
            $consumedBytes += strlen($chunks[$i]);
        }

        return [$output, $consumedBytes];
    }

    private static $instance;
}
