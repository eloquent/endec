<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Hexadecimal;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Hexadecimal\HexadecimalEncodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class HexadecimalEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new HexadecimalEncodeTransform;
    }

    public function transformData()
    {
        //                     input     output          bytesConsumed
        return array(
            'Empty'   => array('',       '',             0),
            '1 byte'  => array('f',      '66',           1),
            '2 bytes' => array('fo',     '666f',         2),
            '3 bytes' => array('foo',    '666f6f',       3),
            '4 bytes' => array('foob',   '666f6f62',     4),
            '5 bytes' => array('fooba',  '666f6f6261',   5),
            '6 bytes' => array('foobar', '666f6f626172', 6),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input));
    }

    /**
     * @dataProvider transformData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, true));
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
