<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base64;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base64\Base64
 * @covers \Eloquent\Endec\AbstractCodec
 */
class Base64Test extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new Base64EncodeTransform;
        $this->decodeTransform = new Base64DecodeTransform;
        $this->codec = new Base64($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base64;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                     decoded   encoded
        return array(
            'Empty'   => array('',       ''),
            '1 byte'  => array('f',      'Zg=='),
            '2 bytes' => array('fo',     'Zm8='),
            '3 bytes' => array('foo',    'Zm9v'),
            '4 bytes' => array('foob',   'Zm9vYg=='),
            '5 bytes' => array('fooba',  'Zm9vYmE='),
            '6 bytes' => array('foobar', 'Zm9vYmFy'),
        );
    }

    /**
     * @dataProvider encodingData
     */
    public function testEncode($decoded, $encoded)
    {
        $this->assertSame($encoded, $this->codec->encode($decoded));
    }

    /**
     * @dataProvider encodingData
     */
    public function testDecode($decoded, $encoded)
    {
        $this->assertSame($decoded, $this->codec->decode($encoded));
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
