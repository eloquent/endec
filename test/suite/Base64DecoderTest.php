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

use Eloquent\Endec\TestCase\AbstractDataTransformTestCase;

/**
 * @covers \Eloquent\Endec\Encoding\Base64Decoder
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64DecoderTest extends AbstractDataTransformTestCase
{
    protected function setUp()
    {
        $this->codec = new Base64Decoder(10);

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
        $this->codec = new Base64Decoder;

        $this->assertSame(8192, $this->codec->bufferSize());
    }

    /**
     * @dataProvider encodingData
     */
    public function testWriteEnd($data)
    {
        $this->codec->write(base64_encode($data));
        $this->codec->end();

        $this->assertSame($data, $this->output);
        $this->assertTrue($this->endEmitted);
        $this->assertTrue($this->closeEmitted);
    }

    /**
     * @dataProvider encodingData
     */
    public function testEnd($data)
    {
        $this->codec->end(base64_encode($data));

        $this->assertSame($data, $this->output);
        $this->assertTrue($this->endEmitted);
        $this->assertTrue($this->closeEmitted);
    }

    public function testEndEmptyString()
    {
        $this->codec->end('');

        $this->assertSame('', $this->output);
    }
}
