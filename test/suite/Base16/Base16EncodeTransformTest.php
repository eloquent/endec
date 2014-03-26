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
        //                     input     output          bytesConsumed context
        return array(
            'Empty'   => array('',       '',             0,            null),
            '1 byte'  => array('f',      '66',           1,            null),
            '2 bytes' => array('fo',     '666F',         2,            null),
            '3 bytes' => array('foo',    '666F6F',       3,            null),
            '4 bytes' => array('foob',   '666F6F62',     4,            null),
            '5 bytes' => array('fooba',  '666F6F6261',   5,            null),
            '6 bytes' => array('foobar', '666F6F626172', 6,            null),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed, $context)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, $actualContext));
        $this->assertSame($context, $actualContext);
    }

    /**
     * @dataProvider transformData
     */
    public function testTransformEnd($input, $output, $bytesConsumed, $context)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, $actualContext, true));
        $this->assertSame($context, $actualContext);
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
