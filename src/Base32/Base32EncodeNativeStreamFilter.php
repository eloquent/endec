<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base32;

use Eloquent\Endec\Transform\AbstractNativeStreamFilter;
use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * A native stream filter for base32 encoding.
 */
class Base32EncodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return DataTransformInterface The data transform.
     */
    protected function createTransform()
    {
        return Base32EncodeTransform::instance();
    }
}
