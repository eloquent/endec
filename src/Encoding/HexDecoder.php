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
 * Decodes data using hexadecimal encoding.
 */
class HexDecoder extends AbstractDataTransform
{
    /**
     * Transform the supplied data chunk.
     *
     * @param string  $data     The data to process.
     * @param boolean $isEnding True if all data should be consumed.
     *
     * @return tuple<string,integer> A 2-tuple of the transformed data, and the number of bytes consumed.
     */
    protected function doTransform($data, $isEnding)
    {
        $consumedBytes = $this->calculateConsumedBytes($data, $isEnding, 2);

        return array(hex2bin(substr($data, 0, $consumedBytes)), $consumedBytes);
    }
}
