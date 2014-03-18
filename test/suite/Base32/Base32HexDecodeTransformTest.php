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
        //                                   input               output        bytesConsumed
        return array(
            'Empty'                 => array('',                 '',           0),
            '1 byte'                => array('C',                '',           0),
            '2 bytes'               => array('CP',               '',           0),
            '3 bytes'               => array('CPN',              '',           0),
            '4 bytes'               => array('CPNM',             '',           0),
            '5 bytes'               => array('CPNMU',            '',           0),
            '6 bytes'               => array('CPNMUO',           '',           0),
            '7 bytes'               => array('CPNMUOJ',          '',           0),
            '8 bytes'               => array('CPNMUOJ1',         'fooba',      8),
            '9 bytes'               => array('CPNMUOJ1E',        'fooba',      8),
            '10 bytes'              => array('CPNMUOJ1E9',       'fooba',      8),
            '11 bytes'              => array('CPNMUOJ1E9H',      'fooba',      8),
            '12 bytes'              => array('CPNMUOJ1E9H6',     'fooba',      8),
            '13 bytes'              => array('CPNMUOJ1E9H62',    'fooba',      8),
            '14 bytes'              => array('CPNMUOJ1E9H62U',   'fooba',      8),
            '15 bytes'              => array('CPNMUOJ1E9H62UJ',  'fooba',      8),
            '16 bytes'              => array('CPNMUOJ1E9H62UJH', 'foobarbazq', 16),
            '16 bytes with padding' => array('CPNMUOJ1E8======', 'foobar',     16),
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
        //                                   input               output        bytesConsumed
        return array(
            'Empty'                 => array('',                 '',           0),
            '2 bytes'               => array('CO',               'f',          2),
            '4 bytes'               => array('CPNG',             'fo',         4),
            '5 bytes'               => array('CPNMU',            'foo',        5),
            '7 bytes'               => array('CPNMUOG',          'foob',       7),
            '8 bytes'               => array('CPNMUOJ1',         'fooba',      8),
            '8 bytes with padding'  => array('CO======',         'f',          8),
            '10 bytes'              => array('CPNMUOJ1E8',       'foobar',     10),
            '12 bytes'              => array('CPNMUOJ1E9H0',     'foobarb',    12),
            '13 bytes'              => array('CPNMUOJ1E9H62',    'foobarba',   13),
            '15 bytes'              => array('CPNMUOJ1E9H62UG',  'foobarbaz',  15),
            '16 bytes'              => array('CPNMUOJ1E9H62UJH', 'foobarbazq', 16),
            '16 bytes with padding' => array('CPNMUOJ1E8======', 'foobar',     16),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, true));
    }

    public function testTransformFailureLength()
    {
        $this->setExpectedException('Eloquent\Endec\Exception\InvalidEncodedDataException');
        $this->transform->transform('A', true);
    }

    public function testTransformFailureAlphabet()
    {
        $this->setExpectedException('Eloquent\Endec\Exception\InvalidEncodedDataException');
        $this->transform->transform('$$$$$$$$', true);
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
