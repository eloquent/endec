<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base16;

use Eloquent\Endec\Transform\TransformStream;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class Base16Test extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new Base16EncodeTransform;
        $this->decodeTransform = new Base16DecodeTransform;
        $this->codec = new Base16($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base16;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                                           decoded   encoded
        return [
            'RFC 4648 base16 test vector 1' => ['',       ''],
            'RFC 4648 base16 test vector 2' => ['f',      '66'],
            'RFC 4648 base16 test vector 3' => ['fo',     '666F'],
            'RFC 4648 base16 test vector 4' => ['foo',    '666F6F'],
            'RFC 4648 base16 test vector 5' => ['foob',   '666F6F62'],
            'RFC 4648 base16 test vector 6' => ['fooba',  '666F6F6261'],
            'RFC 4648 base16 test vector 7' => ['foobar', '666F6F626172'],
        ];
    }

    /**
     * @dataProvider encodingData
     */
    public function testEncode($decoded, $encoded)
    {
        $this->assertSame($encoded, $this->codec->encode($decoded));
    }

    public function testEncodeFullAlphabet()
    {
        $this->assertSame('0123456789ABCDEF', $this->codec->encode(hex2bin('0123456789ABCDEF')));
    }

    /**
     * @dataProvider encodingData
     */
    public function testDecode($decoded, $encoded)
    {
        $this->assertSame($decoded, $this->codec->decode($encoded));
    }

    public function testDecodeFullAlphabet()
    {
        $this->assertSame('0123456789ABCDEF', strtoupper(bin2hex($this->codec->decode('0123456789ABCDEF'))));
    }

    public function testCreateEncodeStream()
    {
        $this->assertEquals(new TransformStream($this->encodeTransform, 111), $this->codec->createEncodeStream(111));
    }

    public function testCreateDecodeStream()
    {
        $this->assertEquals(new TransformStream($this->decodeTransform, 111), $this->codec->createDecodeStream(111));
    }

    public function testInstance()
    {
        $className = get_class($this->codec);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
