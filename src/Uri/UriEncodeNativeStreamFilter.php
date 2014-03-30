<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Uri;

use Eloquent\Confetti\AbstractNativeStreamFilter;
use Eloquent\Confetti\TransformInterface;

/**
 * A native stream filter for URI percent encoding.
 *
 * @link http://tools.ietf.org/html/rfc3986#section-2.1
 */
class UriEncodeNativeStreamFilter extends AbstractNativeStreamFilter
{
    /**
     * Create the transform.
     *
     * @return TransformInterface The data transform.
     */
    protected function createTransform()
    {
        return UriEncodeTransform::instance();
    }
}
