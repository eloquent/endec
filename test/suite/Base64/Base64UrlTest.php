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

use Eloquent\Endec\Transform\TransformStream;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class Base64UrlTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new Base64UrlEncodeTransform;
        $this->decodeTransform = new Base64UrlDecodeTransform;
        $this->codec = new Base64Url($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new Base64Url;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                                           decoded   encoded
        return array(
            'RFC 4648 base64 test vector 1' => array('',       ''),
            'RFC 4648 base64 test vector 2' => array('f',      'Zg'),
            'RFC 4648 base64 test vector 3' => array('fo',     'Zm8'),
            'RFC 4648 base64 test vector 4' => array('foo',    'Zm9v'),
            'RFC 4648 base64 test vector 5' => array('foob',   'Zm9vYg'),
            'RFC 4648 base64 test vector 6' => array('fooba',  'Zm9vYmE'),
            'RFC 4648 base64 test vector 7' => array('foobar', 'Zm9vYmFy'),
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
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_AA',
            $this->codec->encode(
                hex2bin(
                    '00108310518720928b30d38f41149351559761969b71d79f8218a39259a7a29aabb2dbafc31cb3d35db7e39ebbf3dfbf00'
                )
            )
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
            '00108310518720928b30d38f41149351559761969b71d79f8218a39259a7a29aabb2dbafc31cb3d35db7e39ebbf3dfbf00',
            bin2hex($this->codec->decode('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_AA'))
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
