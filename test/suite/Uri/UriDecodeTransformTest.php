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

class UriDecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new UriDecodeTransform;
    }

    public function transformData()
    {
        //                             input           output    consumed context
        return array(
            'Empty'           => array('',             '',       0,       null),

            '1 byte safe'     => array('f',            'f',      1,       null),
            '2 bytes safe'    => array('fo',           'fo',     2,       null),
            '3 bytes safe'    => array('foo',          'foo',    3,       null),
            '4 bytes safe'    => array('foob',         'foob',   4,       null),
            '5 bytes safe'    => array('fooba',        'fooba',  5,       null),
            '6 bytes safe'    => array('foobar',       'foobar', 6,       null),

            '1 bytes encoded' => array('%',            '',       0,       null),
            '2 bytes encoded' => array('%2',           '',       0,       null),
            '3 bytes encoded' => array('%21',          '!',      3,       null),
            '4 bytes encoded' => array('%21%',         '!',      3,       null),
            '5 bytes encoded' => array('%21%4',        '!',      3,       null),
            '6 bytes encoded' => array('%21%40',       '!@',     6,       null),

            '1 byte unsafe'   => array('!',            '!',      1,       null),
            '2 bytes unsafe'  => array('!@',           '!@',     2,       null),
            '3 bytes unsafe'  => array('!@#',          '!@#',    3,       null),
            '4 bytes unsafe'  => array('!@#$',         '!@#$',   4,       null),
            '5 bytes unsafe'  => array('!@#$&',        '!@#$&',  5,       null),
            '6 bytes unsafe'  => array('!@#$&^',       '!@#$&^', 6,       null),

            '1 bytes mixed'   => array('f',            'f',      1,       null),
            '2 bytes mixed'   => array('f%',           'f',      1,       null),
            '3 bytes mixed'   => array('f%2',          'f',      1,       null),
            '4 bytes mixed'   => array('f%21',         'f!',     4,       null),
            '5 bytes mixed'   => array('f%21o',        'f!o',    5,       null),
            '6 bytes mixed'   => array('f%21o%',       'f!o',    5,       null),
            '7 bytes mixed'   => array('f%21o%4',      'f!o',    5,       null),
            '8 bytes mixed'   => array('f%21o%40',     'f!o@',   8,       null),
            '9 bytes mixed'   => array('f%21o%40o',    'f!o@o',  9,       null),
            '10 bytes mixed'  => array('f%21o%40o%',   'f!o@o',  9,       null),
            '11 bytes mixed'  => array('f%21o%40o%2',  'f!o@o',  9,       null),
            '12 bytes mixed'  => array('f%21o%40o%23', 'f!o@o#', 12,      null),
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
        //                             input           output     consumed context
        return array(
            'Empty'           => array('',             '',        0,       null),

            '1 byte safe'     => array('f',            'f',       1,       null),
            '2 bytes safe'    => array('fo',           'fo',      2,       null),
            '3 bytes safe'    => array('foo',          'foo',     3,       null),
            '4 bytes safe'    => array('foob',         'foob',    4,       null),
            '5 bytes safe'    => array('fooba',        'fooba',   5,       null),
            '6 bytes safe'    => array('foobar',       'foobar',  6,       null),

            '3 bytes encoded' => array('%21',          '!',       3,       null),
            '6 bytes encoded' => array('%21%40',       '!@',      6,       null),

            '1 byte unsafe'   => array('!',            '!',       1,       null),
            '2 bytes unsafe'  => array('!@',           '!@',      2,       null),
            '3 bytes unsafe'  => array('!@#',          '!@#',     3,       null),
            '4 bytes unsafe'  => array('!@#$',         '!@#$',    4,       null),
            '5 bytes unsafe'  => array('!@#$&',        '!@#$&',   5,       null),
            '6 bytes unsafe'  => array('!@#$&^',       '!@#$&^',  6,       null),

            '1 bytes mixed'   => array('f',            'f',       1,       null),
            '2 bytes mixed'   => array('f%',           'f%',      2,       null),
            '3 bytes mixed'   => array('f%2',          'f%2',     3,       null),
            '4 bytes mixed'   => array('f%21',         'f!',      4,       null),
            '5 bytes mixed'   => array('f%21o',        'f!o',     5,       null),
            '6 bytes mixed'   => array('f%21o%',       'f!o%',    6,       null),
            '7 bytes mixed'   => array('f%21o%4',      'f!o%4',   7,       null),
            '8 bytes mixed'   => array('f%21o%40',     'f!o@',    8,       null),
            '9 bytes mixed'   => array('f%21o%40o',    'f!o@o',   9,       null),
            '10 bytes mixed'  => array('f%21o%40o%',   'f!o@o%',  10,      null),
            '11 bytes mixed'  => array('f%21o%40o%2',  'f!o@o%2', 11,      null),
            '12 bytes mixed'  => array('f%21o%40o%23', 'f!o@o#',  12,      null),
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

    public function testInstance()
    {
        $className = get_class($this->transform);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
