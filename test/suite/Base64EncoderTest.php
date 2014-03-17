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

use Eloquent\Endec\Stream\TestWritableStream;
use Eloquent\Endec\TestCase\AbstractDataTransformTestCase;

/**
 * @covers \Eloquent\Endec\Encoding\Base64Encoder
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64EncoderTest extends AbstractDataTransformTestCase
{
    protected function setUp()
    {
        $this->transform = new Base64Encoder(10);

        parent::setUp();
    }

    public function testConstructor()
    {
        $this->assertSame(10, $this->transform->bufferSize());
        $this->assertTrue($this->transform->isWritable());
        $this->assertTrue($this->transform->isReadable());
    }

    public function testConstructorDefaults()
    {
        $this->transform = new Base64Encoder;

        $this->assertSame(8192, $this->transform->bufferSize());
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $writeReturn = $this->transform->write($data);
        $this->transform->end();

        $this->assertTrue($writeReturn);
        $this->assertSame(base64_encode($data), $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    /**
     * @dataProvider encodingData
     */
    public function testEnd($data)
    {
        $this->transform->end($data);

        $this->assertSame(base64_encode($data), $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testEndEmptyString()
    {
        $this->transform->end('');

        $this->assertSame('', $this->output);
    }

    public function testClose()
    {
        $this->transform->write('foobarbazqux');
        $this->transform->close();
        $this->transform->close();
        $this->transform->end('doom');

        $this->assertFalse($this->transform->write('splat'));
        $this->assertSame(base64_encode('foobarbazqux'), $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testPauseResume()
    {
        $this->transform->pause();

        $this->assertFalse($this->transform->write('foobarbazqux'));
        $this->assertSame('', $this->output);

        $this->transform->resume();

        $this->assertTrue($this->transform->write('doom'));
        $this->assertSame(base64_encode('foobarbazqux'), $this->output);

        $this->transform->end();

        $this->assertSame(base64_encode('foobarbazquxdoom'), $this->output);
    }

    public function testPipe()
    {
        $destination = new TestWritableStream;
        $this->transform->pipe($destination);
        $this->transform->end('foobarbazquxdoom');

        $this->assertSame(base64_encode('foobarbazquxdoom'), $destination->data);
    }
}
