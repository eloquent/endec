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

use Eloquent\Confetti\AbstractNativeStreamFilter;
use Eloquent\Confetti\TransformInterface;

/**
 * A native stream filter for base64 decoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-4
 */
class Base64DecodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return TransformInterface The data transform.
     */
    protected function createTransform()
    {
        return Base64DecodeTransform::instance();
    }
}
