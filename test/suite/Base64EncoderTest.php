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

/**
 * @covers \Eloquent\Endec\Encoding\Base64Encoder
 * @covers \Eloquent\Endec\Encoding\AbstractCodec
 */
class Base64EncoderTest extends AbstractCodecTest
{
    protected function setUp()
    {
        $this->codec = new Base64Encoder(10);

        parent::setUp();
    }

    public function testConstructor()
    {
        $this->assertSame(10, $this->codec->bufferSize());
        $this->assertTrue($this->codec->isWritable());
        $this->assertTrue($this->codec->isReadable());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base64Encoder;

        $this->assertSame(8192, $this->codec->bufferSize());
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $this->codec->write($data);
        $this->codec->end();

        $this->assertSame(base64_encode($data), $this->output);
        $this->assertTrue($this->endEmitted);
        $this->assertTrue($this->closeEmitted);
    }

    /**
     * @dataProvider encodingData
     */
    public function testEnd($data)
    {
        $this->codec->end($data);

        $this->assertSame(base64_encode($data), $this->output);
        $this->assertTrue($this->endEmitted);
        $this->assertTrue($this->closeEmitted);
    }
}
