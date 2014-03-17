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
 * @covers \Eloquent\Endec\Encoding\HexDecoder
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class HexDecoderTest extends AbstractDataTransformTestCase
{
    protected function setUp()
    {
        $this->transform = new HexDecoder(10);

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
        $this->transform = new HexDecoder;

        $this->assertSame(8192, $this->transform->bufferSize());
    }

    /**
     * @dataProvider encodingData
     */
    public function testTransform($data)
    {
        $this->assertSame($data, $this->transform->transform(bin2hex($data)));
        $this->assertSame('', $this->output);
        $this->assertSame(0, $this->endsEmitted);
        $this->assertSame(0, $this->closesEmitted);
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $writeReturn = $this->transform->write(bin2hex($data));
        $this->transform->end();

        $this->assertTrue($writeReturn);
        $this->assertSame($data, $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    /**
     * @dataProvider encodingData
     */
    public function testEnd($data)
    {
        $this->transform->end(bin2hex($data));

        $this->assertSame($data, $this->output);
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
        $this->transform->write(bin2hex('foobarbazqux'));
        $this->transform->close();
        $this->transform->close();
        $this->transform->end(bin2hex('doom'));

        $this->assertFalse($this->transform->write(bin2hex('splat')));
        $this->assertSame('foobarbazqux', $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testPauseResume()
    {
        $this->transform->pause();

        $this->assertFalse($this->transform->write(bin2hex('foobarbazqux')));
        $this->assertSame('', $this->output);

        $this->transform->resume();

        $this->assertTrue($this->transform->write(bin2hex('doom')));
        $this->assertSame('foobarbazqux', $this->output);

        $this->transform->end();

        $this->assertSame('foobarbazquxdoom', $this->output);
    }

    public function testPipe()
    {
        $destination = new TestWritableStream;
        $this->transform->pipe($destination);
        $this->transform->end(bin2hex('foobarbazquxdoom'));

        $this->assertSame('foobarbazquxdoom', $destination->data);
    }
}
