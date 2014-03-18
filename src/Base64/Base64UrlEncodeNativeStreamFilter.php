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

use Eloquent\Endec\Transform\AbstractNativeStreamFilter;
use Eloquent\Endec\Transform\DataTransformInterface;

/**
 * A native stream filter for base64url encoding.
 */
class Base64UrlEncodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return DataTransformInterface The data transform.
     */
    protected function createTransform()
    {
        return Base64UrlEncodeTransform::instance();
    }
}
