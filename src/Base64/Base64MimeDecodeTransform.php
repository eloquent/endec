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

use Eloquent\Confetti\BufferedTransformInterface;
use Eloquent\Endec\Exception\InvalidEncodedDataException;

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
     * @return BufferedTransformInterface The transform.
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
     * @return tuple<string,integer,mixed> A 3-tuple of the transformed data, the number of bytes consumed, and any resulting error.
     */
    public function transform($data, &$context, $isEnd = false)
    {
        if ($isEnd) {
            list($output, $consumed, $error) = parent::transform(
                preg_replace('{[^[:alnum:]+/=]+}', '', $data),
                $context,
                true
            );

            if (null !== $error) {
                return array(
                    '',
                    0,
                    new InvalidEncodedDataException(
                        'base64mime',
                        $error->data()
                    ),
                );
            }

            return array($output, strlen($data), $error);
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
        $consumed = 0;
        for ($i = 0; $i < $numChunks; $i ++) {
            $buffer .= $chunks[$i][0];
            list($thisOutput, $consumed, $error) = parent::transform(
                $buffer,
                $context
            );

            $output .= $thisOutput;

            if ($consumed === strlen($buffer)) {
                $buffer = '';
                $lastFullyConsumed = $i;
            } else {
                $buffer = substr($buffer, $consumed);
            }
        }

        if ($lastFullyConsumed > -1) {
            if ($lastFullyConsumed < $numChunks - 1) {
                $consumed = $chunks[$lastFullyConsumed + 1][1];
            } else {
                $consumed = $chunks[$lastFullyConsumed][1] +
                    strlen($chunks[$lastFullyConsumed][0]);
            }
        }

        return array($output, $consumed, null);
    }

    private static $instance;
}
