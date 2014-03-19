<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec;

use Eloquent\Endec\Transform\TransformStream;
use Phake;
use PHPUnit_Framework_TestCase;

class CodecTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = Phake::mock('Eloquent\Endec\Transform\DataTransformInterface');
        $this->decodeTransform = Phake::mock('Eloquent\Endec\Transform\DataTransformInterface');
        $this->codec = new Codec($this->encodeTransform, $this->decodeTransform);

        $transformCallback = function ($data, $isEnd = false) {
            return [str_rot13($data), strlen($data)];
        };
        Phake::when($this->encodeTransform)->transform(Phake::anyParameters())
            ->thenGetReturnByLambda($transformCallback);
        Phake::when($this->decodeTransform)->transform(Phake::anyParameters())
            ->thenGetReturnByLambda($transformCallback);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testEncode()
    {
        $this->assertSame('sbbone', $this->codec->encode('foobar'));
    }

    public function testDecode()
    {
        $this->assertSame('foobar', $this->codec->decode('sbbone'));
    }

    public function testCreateEncodeStream()
    {
        $this->assertEquals(new TransformStream($this->encodeTransform, 111), $this->codec->createEncodeStream(111));
    }

    public function testCreateDecodeStream()
    {
        $this->assertEquals(new TransformStream($this->decodeTransform, 111), $this->codec->createDecodeStream(111));
    }
}
