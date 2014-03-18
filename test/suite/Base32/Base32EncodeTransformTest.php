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
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
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
        //                      input         output              bytesConsumed
        return array(
            'Empty'    => array('',           '',                 0),
            '1 byte'   => array('f',          '',                 0),
            '2 bytes'  => array('fo',         '',                 0),
            '3 bytes'  => array('foo',        '',                 0),
            '4 bytes'  => array('foob',       '',                 0),
            '5 bytes'  => array('fooba',      'MZXW6YTB',         5),
            '6 bytes'  => array('foobar',     'MZXW6YTB',         5),
            '7 bytes'  => array('foobarb',    'MZXW6YTB',         5),
            '8 bytes'  => array('foobarba',   'MZXW6YTB',         5),
            '9 bytes'  => array('foobarbaz',  'MZXW6YTB',         5),
            '10 bytes' => array('foobarbazq', 'MZXW6YTBOJRGC6TR', 10),
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
        //                      input         output              bytesConsumed
        return array(
            'Empty'    => array('',           '',                 0),
            '1 byte'   => array('f',          'MY======',         1),
            '2 bytes'  => array('fo',         'MZXQ====',         2),
            '3 bytes'  => array('foo',        'MZXW6===',         3),
            '4 bytes'  => array('foob',       'MZXW6YQ=',         4),
            '5 bytes'  => array('fooba',      'MZXW6YTB',         5),
            '6 bytes'  => array('foobar',     'MZXW6YTBOI======', 6),
            '7 bytes'  => array('foobarb',    'MZXW6YTBOJRA====', 7),
            '8 bytes'  => array('foobarba',   'MZXW6YTBOJRGC===', 8),
            '9 bytes'  => array('foobarbaz',  'MZXW6YTBOJRGC6Q=', 9),
            '10 bytes' => array('foobarbazq', 'MZXW6YTBOJRGC6TR', 10),
        );
    }

    /**
     * @dataProvider transformEndData
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
