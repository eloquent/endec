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
 * Decodes data using base64 encoding.
 */
class Base64Decoder extends AbstractCodec
{
    protected function processInput($data, $isEnding)
    {
        $length = strlen($data);
        if ($isEnding) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % 3);
        }

        return array(
            base64_decode(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }
}
