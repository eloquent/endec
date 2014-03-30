<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eloquent\Confetti\AbstractNativeStreamFilter;

class MultiplyNativeStreamFilter extends AbstractNativeStreamFilter
{
    protected function createTransform()
    {
        return new MultiplyTransform;
    }
}
