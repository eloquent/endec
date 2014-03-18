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
        //                     input     output bytesConsumed
        return array(
            'Empty'   => array('',       '',    0),
            '1 bytes' => array('6',      '',    0),
            '2 bytes' => array('66',     'f',   2),
            '3 bytes' => array('666',    'f',   2),
            '4 bytes' => array('666f',   'fo',  4),
            '5 bytes' => array('666f6',  'fo',  4),
            '6 bytes' => array('666f6f', 'foo', 6),
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
        //                     input     output bytesConsumed
        return array(
            'Empty'   => array('',       '',    0),
            '2 bytes' => array('66',     'f',   2),
            '4 bytes' => array('666f',   'fo',  4),
            '6 bytes' => array('666f6f', 'foo', 6),
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
        $this->transform->transform('$$', true);
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
