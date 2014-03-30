<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base16;

use Eloquent\Confetti\AbstractNativeStreamFilter;
use Eloquent\Confetti\TransformInterface;

/**
 * A native stream filter for base16 (hexadecimal) decoding.
 *
 * @link http://tools.ietf.org/html/rfc4648#section-8
 */
class Base16DecodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return TransformInterface The data transform.
     */
    protected function createTransform()
    {
        return Base16DecodeTransform::instance();
    }
}
