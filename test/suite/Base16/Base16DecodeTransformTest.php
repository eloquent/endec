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

class Base16DecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base16DecodeTransform;
    }

    public function transformData()
    {
        //                     input     output bytesConsumed context
        return array(
            'Empty'   => array('',       '',    0,            null),
            '1 bytes' => array('6',      '',    0,            null),
            '2 bytes' => array('66',     'f',   2,            null),
            '3 bytes' => array('666',    'f',   2,            null),
            '4 bytes' => array('666f',   'fo',  4,            null),
            '5 bytes' => array('666f6',  'fo',  4,            null),
            '6 bytes' => array('666f6f', 'foo', 6,            null),
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

    public function transformEndData()
    {
        //                     input     output bytesConsumed context
        return array(
            'Empty'   => array('',       '',    0,            null),
            '2 bytes' => array('66',     'f',   2,            null),
            '4 bytes' => array('666f',   'fo',  4,            null),
            '6 bytes' => array('666f6f', 'foo', 6,            null),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed, $context)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, $actualContext, true));
        $this->assertSame($context, $actualContext);
    }

    public function invalidTransformEndData()
    {
        //                                    input
        return array(
            'Characters below range' => array('!!'),
            'Characters above range' => array('~~'),
            '1 byte'                 => array('A'),
            '3 bytes'                => array('AAA'),
        );
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
        $this->transform->transform($input, $context, true);
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
