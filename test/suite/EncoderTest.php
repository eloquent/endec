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

class EncoderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = Phake::mock('Eloquent\Confetti\TransformInterface');
        $this->encoder = new Encoder($this->encodeTransform);

        $transformCallback = function ($data, $isEnd = false) {
            return array(str_rot13($data), strlen($data), null);
        };
        Phake::when($this->encodeTransform)->transform(Phake::anyParameters())
            ->thenGetReturnByLambda($transformCallback);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->encoder->encodeTransform());
    }

    public function testEncode()
    {
        $this->assertSame('sbbone', $this->encoder->encode('foobar'));
    }

    public function testEncodeFailure()
    {
        Phake::when($this->encodeTransform)->transform(Phake::anyParameters())->thenReturn(array('', null, new RuntimeException));

        $this->setExpectedException('RuntimeException');
        $this->encoder->encode('foobar');
    }

    public function testCreateEncodeStream()
    {
        $this->assertEquals(new TransformStream($this->encodeTransform, 111), $this->encoder->createEncodeStream(111));
    }
}
