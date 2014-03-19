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
 * @covers \Eloquent\Endec\Base32\Base32DecodeTransform
 * @covers \Eloquent\Endec\Base32\AbstractBase32DecodeTransform
 */
class Base32DecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base32DecodeTransform;
    }

    public function transformData()
    {
        //                              input               output        bytesConsumed
        return [
            'Empty'                 => ['',                 '',           0],
            '1 byte'                => ['M',                '',           0],
            '2 bytes'               => ['MZ',               '',           0],
            '3 bytes'               => ['MZX',              '',           0],
            '4 bytes'               => ['MZXW',             '',           0],
            '5 bytes'               => ['MZXW6',            '',           0],
            '6 bytes'               => ['MZXW6Y',           '',           0],
            '7 bytes'               => ['MZXW6YT',          '',           0],
            '8 bytes'               => ['MZXW6YTB',         'fooba',      8],
            '9 bytes'               => ['MZXW6YTBO',        'fooba',      8],
            '10 bytes'              => ['MZXW6YTBOJ',       'fooba',      8],
            '11 bytes'              => ['MZXW6YTBOJR',      'fooba',      8],
            '12 bytes'              => ['MZXW6YTBOJRG',     'fooba',      8],
            '13 bytes'              => ['MZXW6YTBOJRGC',    'fooba',      8],
            '14 bytes'              => ['MZXW6YTBOJRGC6',   'fooba',      8],
            '15 bytes'              => ['MZXW6YTBOJRGC6T',  'fooba',      8],
            '16 bytes'              => ['MZXW6YTBOJRGC6TR', 'foobarbazq', 16],
            '16 bytes with padding' => ['MZXW6YTBOI======', 'foobar',     16],
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
            '2 bytes'               => ['MY',               'f',          2],
            '4 bytes'               => ['MZXQ',             'fo',         4],
            '5 bytes'               => ['MZXW6',            'foo',        5],
            '7 bytes'               => ['MZXW6YQ',          'foob',       7],
            '8 bytes'               => ['MZXW6YTB',         'fooba',      8],
            '8 bytes with padding'  => ['MY======',         'f',          8],
            '10 bytes'              => ['MZXW6YTBOI',       'foobar',     10],
            '12 bytes'              => ['MZXW6YTBOJRA',     'foobarb',    12],
            '13 bytes'              => ['MZXW6YTBOJRGC',    'foobarba',   13],
            '15 bytes'              => ['MZXW6YTBOJRGC6Q',  'foobarbaz',  15],
            '16 bytes'              => ['MZXW6YTBOJRGC6TR', 'foobarbazq', 16],
            '16 bytes with padding' => ['MZXW6YTBOI======', 'foobar',     16],
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
            'The supplied data is not valid for base32 encoding.'
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
