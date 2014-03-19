<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base16;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base16\Base16DecodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base16DecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base16DecodeTransform;
    }

    public function transformData()
    {
        //                input     output bytesConsumed
        return [
            'Empty'   => ['',       '',    0],
            '1 bytes' => ['6',      '',    0],
            '2 bytes' => ['66',     'f',   2],
            '3 bytes' => ['666',    'f',   2],
            '4 bytes' => ['666f',   'fo',  4],
            '5 bytes' => ['666f6',  'fo',  4],
            '6 bytes' => ['666f6f', 'foo', 6],
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
        //                input     output bytesConsumed
        return [
            'Empty'   => ['',       '',    0],
            '2 bytes' => ['66',     'f',   2],
            '4 bytes' => ['666f',   'fo',  4],
            '6 bytes' => ['666f6f', 'foo', 6],
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
            'Characters below range' => ['!!'],
            'Characters above range' => ['~~'],
            '1 byte'                 => ['A'],
            '3 bytes'                => ['AAA'],
        ];
    }

    /**
     * @dataProvider invalidTransformEndData
     */
    public function testTransformFailure($input)
    {
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base16 encoding.'
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
