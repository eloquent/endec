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

use PHPUnit_Framework_TestCase;

class Base64EncoderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->codec = new Base64Encoder(1024);
    }

    public function testConstructor()
    {
        $this->assertSame(1024, $this->codec->bufferSize());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base64Encoder;

        $this->assertSame(8192, $this->codec->bufferSize());
    }

    public function testEnd()
    {
        $output = '';
        $this->codec->on(
            'data',
            function ($data, $codec) use (&$output) {
                $output .= $data;
            }
        );
        $this->codec->end('foo');

        $this->assertSame(base64_encode('foo'), $output);
    }
}
