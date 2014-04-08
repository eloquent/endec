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

class UriEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new UriEncodeTransform;
    }

    public function transformData()
    {
        //                            input     output                consumed context
        return array(
            'Empty'          => array('',       '',                   0,       null),

            '1 byte safe'    => array('f',      'f',                  1,       null),
            '2 bytes safe'   => array('fo',     'fo',                 2,       null),
            '3 bytes safe'   => array('foo',    'foo',                3,       null),
            '4 bytes safe'   => array('foob',   'foob',               4,       null),
            '5 bytes safe'   => array('fooba',  'fooba',              5,       null),
            '6 bytes safe'   => array('foobar', 'foobar',             6,       null),

            '1 byte unsafe'  => array('!',      '%21',                1,       null),
            '2 bytes unsafe' => array('!@',     '%21%40',             2,       null),
            '3 bytes unsafe' => array('!@#',    '%21%40%23',          3,       null),
            '4 bytes unsafe' => array('!@#$',   '%21%40%23%24',       4,       null),
            '5 bytes unsafe' => array('!@#$%',  '%21%40%23%24%25',    5,       null),
            '6 bytes unsafe' => array('!@#$%^', '%21%40%23%24%25%5E', 6,       null),

            'Mixed safety'   => array('f!o@o#', 'f%21o%40o%23',       6,       null),

            'All reserved characters' => array(
                ':/?#\[\]@!$&\'()*+,;=',
                '%3A%2F%3F%23%5C%5B%5C%5D%40%21%24%26%27%28%29%2A%2B%2C%3B%3D',
                20,
                null
            ),
            'All unreserved characters' => array(
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
                66,
                null
            ),
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

    /**
     * @dataProvider transformData
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
