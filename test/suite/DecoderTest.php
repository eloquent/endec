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

use Eloquent\Confetti\TransformStream;
use PHPUnit_Framework_TestCase;
use Phake;
use RuntimeException;

class DecoderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->decodeTransform = Phake::mock('Eloquent\Confetti\TransformInterface');
        $this->decoder = new Decoder($this->decodeTransform);

        $transformCallback = function ($data, $isEnd = false) {
            return array(str_rot13($data), strlen($data), null);
        };
        Phake::when($this->decodeTransform)->transform(Phake::anyParameters())
            ->thenGetReturnByLambda($transformCallback);
    }

    public function testConstructor()
    {
        $this->assertSame($this->decodeTransform, $this->decoder->decodeTransform());
    }

    public function testDecode()
    {
        $this->assertSame('foobar', $this->decoder->decode('sbbone'));
    }

    public function testDecodeFailure()
    {
        Phake::when($this->decodeTransform)->transform(Phake::anyParameters())->thenReturn(array('', null, new RuntimeException));

        $this->setExpectedException('RuntimeException');
        $this->decoder->decode('foobar');
    }

    public function testCreateDecodeStream()
    {
        $this->assertEquals(new TransformStream($this->decodeTransform, 111), $this->decoder->createDecodeStream(111));
    }
}
