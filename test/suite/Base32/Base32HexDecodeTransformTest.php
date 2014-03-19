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
        //                              input               output        bytesConsumed
        return [
            'Empty'                 => ['',                 '',           0],
            '1 byte'                => ['C',                '',           0],
            '2 bytes'               => ['CP',               '',           0],
            '3 bytes'               => ['CPN',              '',           0],
            '4 bytes'               => ['CPNM',             '',           0],
            '5 bytes'               => ['CPNMU',            '',           0],
            '6 bytes'               => ['CPNMUO',           '',           0],
            '7 bytes'               => ['CPNMUOJ',          '',           0],
            '8 bytes'               => ['CPNMUOJ1',         'fooba',      8],
            '9 bytes'               => ['CPNMUOJ1E',        'fooba',      8],
            '10 bytes'              => ['CPNMUOJ1E9',       'fooba',      8],
            '11 bytes'              => ['CPNMUOJ1E9H',      'fooba',      8],
            '12 bytes'              => ['CPNMUOJ1E9H6',     'fooba',      8],
            '13 bytes'              => ['CPNMUOJ1E9H62',    'fooba',      8],
            '14 bytes'              => ['CPNMUOJ1E9H62U',   'fooba',      8],
            '15 bytes'              => ['CPNMUOJ1E9H62UJ',  'fooba',      8],
            '16 bytes'              => ['CPNMUOJ1E9H62UJH', 'foobarbazq', 16],
            '16 bytes with padding' => ['CPNMUOJ1E8======', 'foobar',     16],
        ];
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input));
    }

    public function transformEndData()
    {
        //                              input               output        bytesConsumed
        return [
            'Empty'                 => ['',                 '',           0],
            '2 bytes'               => ['CO',               'f',          2],
            '4 bytes'               => ['CPNG',             'fo',         4],
            '5 bytes'               => ['CPNMU',            'foo',        5],
            '7 bytes'               => ['CPNMUOG',          'foob',       7],
            '8 bytes'               => ['CPNMUOJ1',         'fooba',      8],
            '8 bytes with padding'  => ['CO======',         'f',          8],
            '10 bytes'              => ['CPNMUOJ1E8',       'foobar',     10],
            '12 bytes'              => ['CPNMUOJ1E9H0',     'foobarb',    12],
            '13 bytes'              => ['CPNMUOJ1E9H62',    'foobarba',   13],
            '15 bytes'              => ['CPNMUOJ1E9H62UG',  'foobarbaz',  15],
            '16 bytes'              => ['CPNMUOJ1E9H62UJH', 'foobarbazq', 16],
            '16 bytes with padding' => ['CPNMUOJ1E8======', 'foobar',     16],
        ];
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input, true));
    }

    public function invalidTransformEndData()
    {
        //                               input
        return [
            'Characters below range' => ['!!!!!!!!'],
            'Characters above range' => ['~~~~~~~~'],
            '1 byte'                 => ['A'],
            '9 bytes'                => ['AAAAAAAAA'],
        ];
    }

    /**
     * @dataProvider invalidTransformEndData
     */
    public function testTransformFailure($input)
    {
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base32hex encoding.'
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
