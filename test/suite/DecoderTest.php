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

/**
 * @covers \Eloquent\Endec\Decoder
 * @covers \Eloquent\Endec\DecoderTrait
 */
class DecoderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->decodeTransform = Phake::mock('Eloquent\Endec\Transform\DataTransformInterface');
        $this->codec = new Decoder($this->decodeTransform);

        $transformCallback = function ($data, $isEnd = false) {
            return array(str_rot13($data), strlen($data));
        };
        Phake::when($this->decodeTransform)->transform(Phake::anyParameters())
            ->thenGetReturnByLambda($transformCallback);
    }

    public function testConstructor()
    {
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testDecode()
    {
        $this->assertSame('foobar', $this->codec->decode('sbbone'));
    }

    public function testCreateDecodeStream()
    {
        $this->assertEquals(new TransformStream($this->decodeTransform, 111), $this->codec->createDecodeStream(111));
    }
}
