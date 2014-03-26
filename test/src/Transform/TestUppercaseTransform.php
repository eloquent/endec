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

class TestUppercaseTransform implements DataTransformInterface
{
    public function transform($data, &$context, $isEnd = false)
    {
        $length = strlen($data);
        if ($isEnd) {
            $consumedBytes = $length;
        } else {
            $consumedBytes = $length - ($length % 2);
        }

        return array(
            strtoupper(substr($data, 0, $consumedBytes)),
            $consumedBytes
        );
    }
}
