<?php

use Eloquent\Endec\Transform\AbstractNativeStreamFilter;

class MultiplyNativeStreamFilter extends AbstractNativeStreamFilter
{
    protected function createTransform()
    {
        return new MultiplyTransform;
    }
}
