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
 * @covers \Eloquent\Endec\Base32\Base32EncodeTransform
 * @covers \Eloquent\Endec\Base32\AbstractBase32EncodeTransform
 */
class Base32EncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base32EncodeTransform;
    }

    public function transformData()
    {
        //                      input         output              bytesConsumed context
        return array(
            'Empty'    => array('',           '',                 0,            null),
            '1 byte'   => array('f',          '',                 0,            null),
            '2 bytes'  => array('fo',         '',                 0,            null),
            '3 bytes'  => array('foo',        '',                 0,            null),
            '4 bytes'  => array('foob',       '',                 0,            null),
            '5 bytes'  => array('fooba',      'MZXW6YTB',         5,            null),
            '6 bytes'  => array('foobar',     'MZXW6YTB',         5,            null),
            '7 bytes'  => array('foobarb',    'MZXW6YTB',         5,            null),
            '8 bytes'  => array('foobarba',   'MZXW6YTB',         5,            null),
            '9 bytes'  => array('foobarbaz',  'MZXW6YTB',         5,            null),
            '10 bytes' => array('foobarbazq', 'MZXW6YTBOJRGC6TR', 10,           null),
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
        //                      input         output              bytesConsumed context
        return array(
            'Empty'    => array('',           '',                 0,            null),
            '1 byte'   => array('f',          'MY======',         1,            null),
            '2 bytes'  => array('fo',         'MZXQ====',         2,            null),
            '3 bytes'  => array('foo',        'MZXW6===',         3,            null),
            '4 bytes'  => array('foob',       'MZXW6YQ=',         4,            null),
            '5 bytes'  => array('fooba',      'MZXW6YTB',         5,            null),
            '6 bytes'  => array('foobar',     'MZXW6YTBOI======', 6,            null),
            '7 bytes'  => array('foobarb',    'MZXW6YTBOJRA====', 7,            null),
            '8 bytes'  => array('foobarba',   'MZXW6YTBOJRGC===', 8,            null),
            '9 bytes'  => array('foobarbaz',  'MZXW6YTBOJRGC6Q=', 9,            null),
            '10 bytes' => array('foobarbazq', 'MZXW6YTBOJRGC6TR', 10,           null),
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

    public function testInstance()
    {
        $className = get_class($this->transform);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
