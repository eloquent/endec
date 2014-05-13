<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base32;

use Eloquent\Confetti\TransformStream;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class Base32HexTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new Base32HexEncodeTransform;
        $this->decodeTransform = new Base32HexDecodeTransform;
        $this->codec = new Base32Hex($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base32Hex;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                                           decoded   encoded
        return array(
            'RFC 4648 base32 test vector 1' => array('',       ''),
            'RFC 4648 base32 test vector 2' => array('f',      'CO======'),
            'RFC 4648 base32 test vector 3' => array('fo',     'CPNG===='),
            'RFC 4648 base32 test vector 4' => array('foo',    'CPNMU==='),
            'RFC 4648 base32 test vector 5' => array('foob',   'CPNMUOG='),
            'RFC 4648 base32 test vector 6' => array('fooba',  'CPNMUOJ1'),
            'RFC 4648 base32 test vector 7' => array('foobar', 'CPNMUOJ1E8======'),
        );
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
        $this->assertSame(
            '0123456789ABCDEFGHIJKLMNOPQRSTUV00======',
            $this->codec->encode(pack('H*', '00443214c74254b635cf84653a56d7c675be77df00'))
        );
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
        $this->assertSame(
            '00443214c74254b635cf84653a56d7c675be77df00',
            bin2hex($this->codec->decode('0123456789ABCDEFGHIJKLMNOPQRSTUV00======'))
        );
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
