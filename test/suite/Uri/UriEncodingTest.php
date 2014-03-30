<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Uri;

use Eloquent\Confetti\TransformStream;
use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class UriEncodingTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->encodeTransform = new UriEncodeTransform;
        $this->decodeTransform = new UriDecodeTransform;
        $this->codec = new UriEncoding($this->encodeTransform, $this->decodeTransform);
    }

    public function testConstructor()
    {
        $this->assertSame($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertSame($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function testConstructorDefaults()
    {
        $this->codec = new UriEncoding;

        $this->assertEquals($this->encodeTransform, $this->codec->encodeTransform());
        $this->assertEquals($this->decodeTransform, $this->codec->decodeTransform());
    }

    public function encodingData()
    {
        //                            decoded   encoded
        return array(
            'Empty'          => array('',       ''),

            '1 byte safe'    => array('f',      'f'),
            '2 bytes safe'   => array('fo',     'fo'),
            '3 bytes safe'   => array('foo',    'foo'),
            '4 bytes safe'   => array('foob',   'foob'),
            '5 bytes safe'   => array('fooba',  'fooba'),
            '6 bytes safe'   => array('foobar', 'foobar'),

            '1 byte unsafe'  => array('!',      '%21'),
            '2 bytes unsafe' => array('!@',     '%21%40'),
            '3 bytes unsafe' => array('!@#',    '%21%40%23'),
            '4 bytes unsafe' => array('!@#$',   '%21%40%23%24'),
            '5 bytes unsafe' => array('!@#$%',  '%21%40%23%24%25'),
            '6 bytes unsafe' => array('!@#$%^', '%21%40%23%24%25%5E'),

            'Mixed safety'   => array('f!o@o#', 'f%21o%40o%23'),

            'All reserved characters' => array(
                ':/?#\[\]@!$&\'()*+,;=',
                '%3A%2F%3F%23%5C%5B%5C%5D%40%21%24%26%27%28%29%2A%2B%2C%3B%3D',
            ),
            'All unreserved characters' => array(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
            ),
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
