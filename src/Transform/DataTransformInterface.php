<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Transform;

/**
 * The interface implemented by data transforms.
 */
interface DataTransformInterface
{
    /**
     * Transform the supplied data.
     *
     * This method may transform only part of the supplied data. The return
     * value includes information about how much data was actually consumed. The
     * transform can be forced to consume all data by passing a boolean true as
     * the second argument.
     *
     * @param string $data The data to transform.
     * @param boolean $isEnd True if all supplied data must be transformed.
     *
     * @return tuple<string,integer>                 A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
     */
    public function transform($data, $isEnd = false);
}