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

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base32\Base32HexDecodeTransform
 * @covers \Eloquent\Endec\Base32\AbstractBase32DecodeTransform
 */
class Base32HexDecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base32HexDecodeTransform;
    }

    public function transformData()
    {
        //                                   input               output        consumed context
        return array(
            'Empty'                 => array('',                 '',           0,       null),
            '1 byte'                => array('C',                '',           0,       null),
            '2 bytes'               => array('CP',               '',           0,       null),
            '3 bytes'               => array('CPN',              '',           0,       null),
            '4 bytes'               => array('CPNM',             '',           0,       null),
            '5 bytes'               => array('CPNMU',            '',           0,       null),
            '6 bytes'               => array('CPNMUO',           '',           0,       null),
            '7 bytes'               => array('CPNMUOJ',          '',           0,       null),
            '8 bytes'               => array('CPNMUOJ1',         'fooba',      8,       null),
            '9 bytes'               => array('CPNMUOJ1E',        'fooba',      8,       null),
            '10 bytes'              => array('CPNMUOJ1E9',       'fooba',      8,       null),
            '11 bytes'              => array('CPNMUOJ1E9H',      'fooba',      8,       null),
            '12 bytes'              => array('CPNMUOJ1E9H6',     'fooba',      8,       null),
            '13 bytes'              => array('CPNMUOJ1E9H62',    'fooba',      8,       null),
            '14 bytes'              => array('CPNMUOJ1E9H62U',   'fooba',      8,       null),
            '15 bytes'              => array('CPNMUOJ1E9H62UJ',  'fooba',      8,       null),
            '16 bytes'              => array('CPNMUOJ1E9H62UJH', 'foobarbazq', 16,      null),
            '16 bytes with padding' => array('CPNMUOJ1E8======', 'foobar',     16,      null),
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
        //                                   input               output        consumed context
        return array(
            'Empty'                 => array('',                 '',           0,       null),
            '2 bytes'               => array('CO',               'f',          2,       null),
            '4 bytes'               => array('CPNG',             'fo',         4,       null),
            '5 bytes'               => array('CPNMU',            'foo',        5,       null),
            '7 bytes'               => array('CPNMUOG',          'foob',       7,       null),
            '8 bytes'               => array('CPNMUOJ1',         'fooba',      8,       null),
            '8 bytes with padding'  => array('CO======',         'f',          8,       null),
            '10 bytes'              => array('CPNMUOJ1E8',       'foobar',     10,      null),
            '12 bytes'              => array('CPNMUOJ1E9H0',     'foobarb',    12,      null),
            '13 bytes'              => array('CPNMUOJ1E9H62',    'foobarba',   13,      null),
            '15 bytes'              => array('CPNMUOJ1E9H62UG',  'foobarbaz',  15,      null),
            '16 bytes'              => array('CPNMUOJ1E9H62UJH', 'foobarbazq', 16,      null),
            '16 bytes with padding' => array('CPNMUOJ1E8======', 'foobar',     16,      null),
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
        //                                    input
        return array(
            'Characters below range' => array('!!!!!!!!'),
            'Characters above range' => array('~~~~~~~~'),
            '1 byte'                 => array('A'),
            '9 bytes'                => array('AAAAAAAAA'),
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
            'The supplied data is not valid for base32hex encoding.'
        );
        throw $error;
    }

    public function testBufferSize()
    {
        $this->assertSame(8, $this->transform->bufferSize());
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
