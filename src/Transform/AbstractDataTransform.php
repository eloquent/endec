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
     * @param integer $size      The available data size.
     * @param integer $blockSize The encoding block size.
     * @param boolean $isEnd     True if all data should be consumed.
     *
     * @return integer The amount of data to consume in bytes.
     */
    protected function blocksSize($size, $blockSize, $isEnd)
    {
        if ($isEnd) {
            return $size;
        }

        return $size - ($size % $blockSize);
    }
}
