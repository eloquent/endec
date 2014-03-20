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
 * @covers \Eloquent\Endec\Uri\UriEncodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class UriEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new UriEncodeTransform;
    }

    public function transformData()
    {
        //                       input     output                bytesConsumed
        return [
            'Empty'          => ['',       '',                   0],

            '1 byte safe'    => ['f',      'f',                  1],
            '2 bytes safe'   => ['fo',     'fo',                 2],
            '3 bytes safe'   => ['foo',    'foo',                3],
            '4 bytes safe'   => ['foob',   'foob',               4],
            '5 bytes safe'   => ['fooba',  'fooba',              5],
            '6 bytes safe'   => ['foobar', 'foobar',             6],

            '1 byte unsafe'  => ['!',      '%21',                1],
            '2 bytes unsafe' => ['!@',     '%21%40',             2],
            '3 bytes unsafe' => ['!@#',    '%21%40%23',          3],
            '4 bytes unsafe' => ['!@#$',   '%21%40%23%24',       4],
            '5 bytes unsafe' => ['!@#$%',  '%21%40%23%24%25',    5],
            '6 bytes unsafe' => ['!@#$%^', '%21%40%23%24%25%5E', 6],

            'Mixed safety'   => ['f!o@o#', 'f%21o%40o%23',       6],

            'All reserved characters' => [
                ':/?#\[\]@!$&\'()*+,;=',
                '%3A%2F%3F%23%5C%5B%5C%5D%40%21%24%26%27%28%29%2A%2B%2C%3B%3D',
                20
            ],
            'All unreserved characters' => [
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.~',
                66
            ],
        ];
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input));
    }

    /**
     * @dataProvider transformData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input, true));
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
