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
 * Encodes data using base64 encoding.
 */
class Base64Encoder extends AbstractCodec
{
    protected function processInput($data, $isEnding)
    {
        $length = strlen($data);
        if ($isEnding) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % 2);
        }

        return array(
            base64_encode(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }
}
