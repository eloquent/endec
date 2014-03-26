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

class TestTransform implements DataTransformInterface
{
    public function transform($data, &$context, $isEnd = false)
    {
        $count = $this->count++;

        if (array_key_exists($count, $this->callbacks)) {
            $callback = $this->callbacks[$count];
        } else {
            $callback = $this->callbacks[count($this->callbacks) - 1];
        }

        return $callback($data, $context, $isEnd);
    }

    public $count = 0;
    public $callbacks = array();
}
