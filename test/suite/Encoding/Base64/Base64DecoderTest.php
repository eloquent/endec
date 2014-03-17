<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Encoding\Base64;

use Eloquent\Endec\Stream\TestWritableStream;
use Eloquent\Endec\TestCase\AbstractDataTransformTestCase;

/**
 * @covers \Eloquent\Endec\Encoding\Base64\Base64Decoder
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64DecoderTest extends AbstractDataTransformTestCase
{
    protected function setUp()
    {
        $this->transform = new Base64Decoder(10);

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
        $this->transform = new Base64Decoder;

        $this->assertSame(8192, $this->transform->bufferSize());
    }

    /**
     * @dataProvider encodingData
     */
    public function testTransform($data)
    {
        $this->assertSame($data, $this->transform->transform(base64_encode($data)));
        $this->assertSame('', $this->output);
        $this->assertSame(0, $this->endsEmitted);
        $this->assertSame(0, $this->closesEmitted);
    }

    public function testTransformFailure()
    {
        $this->setExpectedException('Eloquent\Endec\Encoding\Exception\InvalidEncodedDataException');
        $this->transform->transform('$');
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $writeReturn = $this->transform->write(base64_encode($data));
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
        $this->transform->end(base64_encode($data));

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
        $this->transform->write(base64_encode('foobarbazqux'));
        $this->transform->close();
        $this->transform->close();
        $this->transform->end(base64_encode('doom'));

        $this->assertFalse($this->transform->write(base64_encode('splat')));
        $this->assertSame('foobarbazqux', $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testPauseResume()
    {
        $this->transform->pause();

        $this->assertFalse($this->transform->write(base64_encode('foobarbazqux')));
        $this->assertSame('', $this->output);

        $this->transform->resume();

        $this->assertTrue($this->transform->write(base64_encode('doom')));
        $this->assertSame('foobarbazqux', $this->output);

        $this->transform->end();

        $this->assertSame('foobarbazquxdoom', $this->output);
    }

    public function testPipe()
    {
        $destination = new TestWritableStream;
        $this->transform->pipe($destination);
        $this->transform->end(base64_encode('foobarbazquxdoom'));

        $this->assertSame('foobarbazquxdoom', $destination->data);
    }
}
