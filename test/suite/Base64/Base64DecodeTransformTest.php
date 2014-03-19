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
 * @covers \Eloquent\Endec\Base64\Base64DecodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64DecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64DecodeTransform;
    }

    public function transformData()
    {
        //                input       output    bytesConsumed
        return [
            'Empty'   => ['',         '',       0],
            '1 byte'  => ['Z',        '',       0],
            '2 bytes' => ['Zm',       '',       0],
            '3 bytes' => ['Zm9',      '',       0],
            '4 bytes' => ['Zm9v',     'foo',    4],
            '5 bytes' => ['Zm9vY',    'foo',    4],
            '6 bytes' => ['Zm9vYm',   'foo',    4],
            '7 bytes' => ['Zm9vYmF',  'foo',    4],
            '8 bytes' => ['Zm9vYmFy', 'foobar', 8],
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
        //                             input       output    bytesConsumed
        return [
            'Empty'                => ['',         '',       0],
            '2 bytes'              => ['Zm',       'f',      2],
            '3 bytes'              => ['Zm9',      'fo',     3],
            '4 bytes'              => ['Zm9v',     'foo',    4],
            '6 bytes'              => ['Zm9vYm',   'foob',   6],
            '7 bytes'              => ['Zm9vYmF',  'fooba',  7],
            '8 bytes'              => ['Zm9vYmFy', 'foobar', 8],
            '8 bytes with padding' => ['Zm9vYg==', 'foob',   8],
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
            'Characters below range' => ['!!!!'],
            'Characters above range' => ['~~~~'],
            '1 byte'                 => ['A'],
            '5 bytes'                => ['AAAAA'],
        ];
    }

    /**
     * @dataProvider invalidTransformEndData
     */
    public function testTransformFailure($input)
    {
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base64 encoding.'
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
