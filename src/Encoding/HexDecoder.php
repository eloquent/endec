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

/**
 * Decodes data using hexadecimal encoding.
 */
class HexDecoder extends AbstractCodec
{
    protected function processInput($data, $isEnding)
    {
        $length = strlen($data);
        if ($isEnding) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % 2);
        }

        return array(hex2bin(substr($data, 0, $consumedBytes)), $consumedBytes);
    }
}
