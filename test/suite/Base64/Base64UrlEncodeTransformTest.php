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

class Base64UrlEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64UrlEncodeTransform;
    }

    public function transformData()
    {
        //                     input     output      consumed context
        return array(
            'Empty'   => array('',       '',         0,       null),
            '1 byte'  => array('f',      '',         0,       null),
            '2 bytes' => array('fo',     '',         0,       null),
            '3 bytes' => array('foo',    'Zm9v',     3,       null),
            '4 bytes' => array('foob',   'Zm9v',     3,       null),
            '5 bytes' => array('fooba',  'Zm9v',     3,       null),
            '6 bytes' => array('foobar', 'Zm9vYmFy', 6,       null),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext));
        $this->assertSame($context, $actualContext);
    }

    public function transformEndData()
    {
        //                     input     output      consumed context
        return array(
            'Empty'   => array('',       '',         0,       null),
            '1 byte'  => array('f',      'Zg',       1,       null),
            '2 bytes' => array('fo',     'Zm8',      2,       null),
            '3 bytes' => array('foo',    'Zm9v',     3,       null),
            '4 bytes' => array('foob',   'Zm9vYg',   4,       null),
            '5 bytes' => array('fooba',  'Zm9vYmE',  5,       null),
            '6 bytes' => array('foobar', 'Zm9vYmFy', 6,       null),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext, true));
        $this->assertSame($context, $actualContext);
    }

    public function testBufferSize()
    {
        $this->assertSame(3, $this->transform->bufferSize());
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
