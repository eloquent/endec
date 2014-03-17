<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Encoding;

use Eloquent\Endec\Transform\AbstractDataTransform;

/**
 * Encodes data using hexadecimal encoding.
 */
class HexEncoder extends AbstractDataTransform
{
    /**
     * Transform the supplied data chunk.
     *
     * @param string  $data  The data to process.
     * @param boolean $isEnd True if all data should be consumed.
     *
     * @return tuple<string,integer> A 2-tuple of the transformed data, and the number of bytes consumed.
     */
    protected function doTransform($data, $isEnd)
    {
        return array(bin2hex($data), strlen($data));
    }
}
