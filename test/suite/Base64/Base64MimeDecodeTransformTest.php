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
 * @covers \Eloquent\Endec\Base64\Base64MimeDecodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64MimeDecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64MimeDecodeTransform;
    }

    public function transformData()
    {
        //                                 input                   output    bytesConsumed
        return array(
            'Empty'               => array('',                     '',       0),

            '1 byte'              => array('Z',                    '',       0),
            '2 bytes'             => array('Zm',                   '',       0),
            '3 bytes'             => array('Zm9',                  '',       0),
            '4 bytes'             => array('Zm9v',                 'foo',    4),
            '5 bytes'             => array('Zm9vY',                'foo',    4),
            '6 bytes'             => array('Zm9vYm',               'foo',    4),
            '7 bytes'             => array('Zm9vYmF',              'foo',    4),
            '8 bytes'             => array('Zm9vYmFy',             'foobar', 8),

            '1 bytes with skips'  => array("!",                    '',       1),
            '2 bytes with skips'  => array("!Z",                   '',       1),
            '3 bytes with skips'  => array("!Z!",                  '',       1),
            '4 bytes with skips'  => array("!Z!m",                 '',       1),
            '5 bytes with skips'  => array("!Z!m!",                '',       1),
            '6 bytes with skips'  => array("!Z!m!9",               '',       1),
            '7 bytes with skips'  => array("!Z!m!9!",              '',       1),
            '8 bytes with skips'  => array("!Z!m!9!v",             'foo',    8),
            '9 bytes with skips'  => array("!Z!m!9!v\r",           'foo',    9),
            '10 bytes with skips' => array("!Z!m!9!v\r\n",         'foo',    10),
            '11 bytes with skips' => array("!Z!m!9!v\r\nY",        'foo',    10),
            '12 bytes with skips' => array("!Z!m!9!v\r\nY!",       'foo',    10),
            '13 bytes with skips' => array("!Z!m!9!v\r\nY!m",      'foo',    10),
            '14 bytes with skips' => array("!Z!m!9!v\r\nY!m!",     'foo',    10),
            '15 bytes with skips' => array("!Z!m!9!v\r\nY!m!F",    'foo',    10),
            '16 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!",   'foo',    10),
            '17 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!y",  'foobar', 17),
            '18 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!y!", 'foobar', 18),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input));
    }

    public function transformEndData()
    {
        //                                  input                   output    bytesConsumed
        return array(
            'Empty'                => array('',                     '',       0),

            '2 bytes'              => array('Zm',                   'f',      2),
            '3 bytes'              => array('Zm9',                  'fo',     3),
            '4 bytes'              => array('Zm9v',                 'foo',    4),
            '6 bytes'              => array('Zm9vYm',               'foob',   6),
            '7 bytes'              => array('Zm9vYmF',              'fooba',  7),
            '8 bytes'              => array('Zm9vYmFy',             'foobar', 8),
            '8 bytes with padding' => array('Zm9vYg==',             'foob',   8),

            '1 bytes with skips'   => array("!",                    '',       1),
            '4 bytes with skips'   => array("!Z!m",                 'f',      4),
            '5 bytes with skips'   => array("!Z!m!",                'f',      5),
            '6 bytes with skips'   => array("!Z!m!9",               'fo',     6),
            '7 bytes with skips'   => array("!Z!m!9!",              'fo',     7),
            '8 bytes with skips'   => array("!Z!m!9!v",             'foo',    8),
            '9 bytes with skips'   => array("!Z!m!9!v\r",           'foo',    9),
            '10 bytes with skips'  => array("!Z!m!9!v\r\n",         'foo',    10),
            '13 bytes with skips'  => array("!Z!m!9!v\r\nY!m",      'foob',   13),
            '14 bytes with skips'  => array("!Z!m!9!v\r\nY!m!",     'foob',   14),
            '15 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F",    'fooba',  15),
            '16 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!",   'fooba',  16),
            '17 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!y",  'foobar', 17),
            '18 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!y!", 'foobar', 18),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, true));
    }

    public function invalidTransformEndData()
    {
        //                     input
        return array(
            '1 byte'  => array('A'),
            '5 bytes' => array('AAAAA'),
        );
    }

    /**
     * @dataProvider invalidTransformEndData
     */
    public function testTransformFailure($input)
    {
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base64mime encoding.'
        );
        $this->transform->transform($input, true);
    }

    public function testInstance()
    {
        $className = get_class($this->transform);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
