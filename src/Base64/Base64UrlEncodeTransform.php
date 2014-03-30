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

use Eloquent\Confetti\AbstractTransform;
use Eloquent\Confetti\TransformInterface;
use Exception;

/**
 * Encodes data using base64url encoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-5
 */
class Base64UrlEncodeTransform extends AbstractTransform
{
    /**
     * Get the static instance of this transform.
     *
     * @return TransformInterface The transform.
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
     * @return tuple<string,integer> A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws Exception             If the data cannot be transformed.
     */
    public function transform($data, &$context, $isEnd = false)
    {
        $consume = $this->blocksSize(strlen($data), 3, $isEnd);
        if (!$consume) {
            return array('', 0);
        }

        return array(
            rtrim(
                strtr(
                    base64_encode(substr($data, 0, $consume)),
                    '+/',
                    '-_'
                ),
                '='
            ),
            $consume
        );
    }

    private static $instance;
}
