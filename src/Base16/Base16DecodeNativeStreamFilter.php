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

use Eloquent\Endec\Transform\AbstractNativeStreamFilter;
use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * A native stream filter for base16 (hexadecimal) decoding.
 */
class Base16DecodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return DataTransformInterface The data transform.
     */
    protected function createTransform()
    {
        return Base16DecodeTransform::instance();
    }
}
