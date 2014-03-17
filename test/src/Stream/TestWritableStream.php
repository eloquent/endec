<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Stream;

use React\Stream\WritableStream;

class TestWritableStream extends WritableStream
{
    public function write($data)
    {
        $this->data .= $data;
    }

    public $data = '';
}
