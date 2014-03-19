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
 * @covers \Eloquent\Endec\Base16\Base16EncodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base16EncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base16EncodeTransform;
    }

    public function transformData()
    {
        //                input     output          bytesConsumed
        return [
            'Empty'   => ['',       '',             0],
            '1 byte'  => ['f',      '66',           1],
            '2 bytes' => ['fo',     '666F',         2],
            '3 bytes' => ['foo',    '666F6F',       3],
            '4 bytes' => ['foob',   '666F6F62',     4],
            '5 bytes' => ['fooba',  '666F6F6261',   5],
            '6 bytes' => ['foobar', '666F6F626172', 6],
        ];
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input));
    }

    /**
     * @dataProvider transformData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input, true));
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
