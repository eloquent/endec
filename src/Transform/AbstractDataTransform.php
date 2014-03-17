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
 * An abstract base class for implementing data transforms.
 */
abstract class AbstractDataTransform implements DataTransformInterface
{
    /**
     * Calculate the number of bytes to consume based on the encoding's block
     * size and the size of the available data.
     *
     * @param string  $data      The data to consume.
     * @param boolean $isEnd     True if all data should be consumed.
     * @param integer $chunkSize The encoding chunk size.
     *
     * @return integer The amount of data to consume in bytes.
     */
    protected function calculateConsumeBytes($data, $isEnd, $chunkSize)
    {
        $length = strlen($data);
        if ($isEnd) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % $chunkSize);
        }

        return $consumedBytes;
    }
}
