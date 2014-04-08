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

class Base64MimeDecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64MimeDecodeTransform;
    }

    public function transformData()
    {
        //                                 input                   output    consumed context
        return array(
            'Empty'               => array('',                     '',       0,       null),

            '1 byte'              => array('Z',                    '',       0,       null),
            '2 bytes'             => array('Zm',                   '',       0,       null),
            '3 bytes'             => array('Zm9',                  '',       0,       null),
            '4 bytes'             => array('Zm9v',                 'foo',    4,       null),
            '5 bytes'             => array('Zm9vY',                'foo',    4,       null),
            '6 bytes'             => array('Zm9vYm',               'foo',    4,       null),
            '7 bytes'             => array('Zm9vYmF',              'foo',    4,       null),
            '8 bytes'             => array('Zm9vYmFy',             'foobar', 8,       null),

            '1 bytes with skips'  => array("!",                    '',       1,       null),
            '2 bytes with skips'  => array("!Z",                   '',       1,       null),
            '3 bytes with skips'  => array("!Z!",                  '',       1,       null),
            '4 bytes with skips'  => array("!Z!m",                 '',       1,       null),
            '5 bytes with skips'  => array("!Z!m!",                '',       1,       null),
            '6 bytes with skips'  => array("!Z!m!9",               '',       1,       null),
            '7 bytes with skips'  => array("!Z!m!9!",              '',       1,       null),
            '8 bytes with skips'  => array("!Z!m!9!v",             'foo',    8,       null),
            '9 bytes with skips'  => array("!Z!m!9!v\r",           'foo',    9,       null),
            '10 bytes with skips' => array("!Z!m!9!v\r\n",         'foo',    10,      null),
            '11 bytes with skips' => array("!Z!m!9!v\r\nY",        'foo',    10,      null),
            '12 bytes with skips' => array("!Z!m!9!v\r\nY!",       'foo',    10,      null),
            '13 bytes with skips' => array("!Z!m!9!v\r\nY!m",      'foo',    10,      null),
            '14 bytes with skips' => array("!Z!m!9!v\r\nY!m!",     'foo',    10,      null),
            '15 bytes with skips' => array("!Z!m!9!v\r\nY!m!F",    'foo',    10,      null),
            '16 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!",   'foo',    10,      null),
            '17 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!y",  'foobar', 17,      null),
            '18 bytes with skips' => array("!Z!m!9!v\r\nY!m!F!y!", 'foobar', 18,      null),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext));
        $this->assertSame($context, $actualContext);
    }

    public function transformEndData()
    {
        //                                  input                   output    consumed context
        return array(
            'Empty'                => array('',                     '',       0,       null),

            '2 bytes'              => array('Zm',                   'f',      2,       null),
            '3 bytes'              => array('Zm9',                  'fo',     3,       null),
            '4 bytes'              => array('Zm9v',                 'foo',    4,       null),
            '6 bytes'              => array('Zm9vYm',               'foob',   6,       null),
            '7 bytes'              => array('Zm9vYmF',              'fooba',  7,       null),
            '8 bytes'              => array('Zm9vYmFy',             'foobar', 8,       null),
            '8 bytes with padding' => array('Zm9vYg==',             'foob',   8,       null),

            '1 bytes with skips'   => array("!",                    '',       1,       null),
            '4 bytes with skips'   => array("!Z!m",                 'f',      4,       null),
            '5 bytes with skips'   => array("!Z!m!",                'f',      5,       null),
            '6 bytes with skips'   => array("!Z!m!9",               'fo',     6,       null),
            '7 bytes with skips'   => array("!Z!m!9!",              'fo',     7,       null),
            '8 bytes with skips'   => array("!Z!m!9!v",             'foo',    8,       null),
            '9 bytes with skips'   => array("!Z!m!9!v\r",           'foo',    9,       null),
            '10 bytes with skips'  => array("!Z!m!9!v\r\n",         'foo',    10,      null),
            '13 bytes with skips'  => array("!Z!m!9!v\r\nY!m",      'foob',   13,      null),
            '14 bytes with skips'  => array("!Z!m!9!v\r\nY!m!",     'foob',   14,      null),
            '15 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F",    'fooba',  15,      null),
            '16 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!",   'fooba',  16,      null),
            '17 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!y",  'foobar', 17,      null),
            '18 bytes with skips'  => array("!Z!m!9!v\r\nY!m!F!y!", 'foobar', 18,      null),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext, true));
        $this->assertSame($context, $actualContext);
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
        list($output, $consumed, $error) = $this->transform->transform($input, $context, true);

        $this->assertSame('', $output);
        $this->assertSame(0, $consumed);
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base64mime encoding.'
        );
        throw $error;
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
