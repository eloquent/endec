<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Uri;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Uri\UriDecodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class UriDecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new UriDecodeTransform;
    }

    public function transformData()
    {
        //                             input           output    bytesConsumed
        return array(
            'Empty'           => array('',             '',       0),

            '1 byte safe'     => array('f',            'f',      1),
            '2 bytes safe'    => array('fo',           'fo',     2),
            '3 bytes safe'    => array('foo',          'foo',    3),
            '4 bytes safe'    => array('foob',         'foob',   4),
            '5 bytes safe'    => array('fooba',        'fooba',  5),
            '6 bytes safe'    => array('foobar',       'foobar', 6),

            '1 bytes encoded' => array('%',            '',       0),
            '2 bytes encoded' => array('%2',           '',       0),
            '3 bytes encoded' => array('%21',          '!',      3),
            '4 bytes encoded' => array('%21%',         '!',      3),
            '5 bytes encoded' => array('%21%4',        '!',      3),
            '6 bytes encoded' => array('%21%40',       '!@',     6),

            '1 byte unsafe'   => array('!',            '!',      1),
            '2 bytes unsafe'  => array('!@',           '!@',     2),
            '3 bytes unsafe'  => array('!@#',          '!@#',    3),
            '4 bytes unsafe'  => array('!@#$',         '!@#$',   4),
            '5 bytes unsafe'  => array('!@#$&',        '!@#$&',  5),
            '6 bytes unsafe'  => array('!@#$&^',       '!@#$&^', 6),

            '1 bytes mixed'   => array('f',            'f',      1),
            '2 bytes mixed'   => array('f%',           'f',      1),
            '3 bytes mixed'   => array('f%2',          'f',      1),
            '4 bytes mixed'   => array('f%21',         'f!',     4),
            '5 bytes mixed'   => array('f%21o',        'f!o',    5),
            '6 bytes mixed'   => array('f%21o%',       'f!o',    5),
            '7 bytes mixed'   => array('f%21o%4',      'f!o',    5),
            '8 bytes mixed'   => array('f%21o%40',     'f!o@',   8),
            '9 bytes mixed'   => array('f%21o%40o',    'f!o@o',  9),
            '10 bytes mixed'  => array('f%21o%40o%',   'f!o@o',  9),
            '11 bytes mixed'  => array('f%21o%40o%2',  'f!o@o',  9),
            '12 bytes mixed'  => array('f%21o%40o%23', 'f!o@o#', 12),
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
        //                             input           output     bytesConsumed
        return array(
            'Empty'           => array('',             '',        0),

            '1 byte safe'     => array('f',            'f',       1),
            '2 bytes safe'    => array('fo',           'fo',      2),
            '3 bytes safe'    => array('foo',          'foo',     3),
            '4 bytes safe'    => array('foob',         'foob',    4),
            '5 bytes safe'    => array('fooba',        'fooba',   5),
            '6 bytes safe'    => array('foobar',       'foobar',  6),

            '3 bytes encoded' => array('%21',          '!',       3),
            '6 bytes encoded' => array('%21%40',       '!@',      6),

            '1 byte unsafe'   => array('!',            '!',       1),
            '2 bytes unsafe'  => array('!@',           '!@',      2),
            '3 bytes unsafe'  => array('!@#',          '!@#',     3),
            '4 bytes unsafe'  => array('!@#$',         '!@#$',    4),
            '5 bytes unsafe'  => array('!@#$&',        '!@#$&',   5),
            '6 bytes unsafe'  => array('!@#$&^',       '!@#$&^',  6),

            '1 bytes mixed'   => array('f',            'f',       1),
            '2 bytes mixed'   => array('f%',           'f%',      2),
            '3 bytes mixed'   => array('f%2',          'f%2',     3),
            '4 bytes mixed'   => array('f%21',         'f!',      4),
            '5 bytes mixed'   => array('f%21o',        'f!o',     5),
            '6 bytes mixed'   => array('f%21o%',       'f!o%',    6),
            '7 bytes mixed'   => array('f%21o%4',      'f!o%4',   7),
            '8 bytes mixed'   => array('f%21o%40',     'f!o@',    8),
            '9 bytes mixed'   => array('f%21o%40o',    'f!o@o',   9),
            '10 bytes mixed'  => array('f%21o%40o%',   'f!o@o%',  10),
            '11 bytes mixed'  => array('f%21o%40o%2',  'f!o@o%2', 11),
            '12 bytes mixed'  => array('f%21o%40o%23', 'f!o@o#',  12),
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
