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

/**
 * @covers \Eloquent\Endec\Base64\Base64UrlEncodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base64UrlEncodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base64UrlEncodeTransform;
    }

    public function transformData()
    {
        //                input     output      bytesConsumed
        return [
            'Empty'   => ['',       '',         0],
            '1 byte'  => ['f',      '',         0],
            '2 bytes' => ['fo',     '',         0],
            '3 bytes' => ['foo',    'Zm9v',     3],
            '4 bytes' => ['foob',   'Zm9v',     3],
            '5 bytes' => ['fooba',  'Zm9v',     3],
            '6 bytes' => ['foobar', 'Zm9vYmFy', 6],
        ];
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame([$output, $bytesConsumed], $this->transform->transform($input));
    }

    public function transformEndData()
    {
        //                input     output      bytesConsumed
        return [
            'Empty'   => ['',       '',         0],
            '1 byte'  => ['f',      'Zg',       1],
            '2 bytes' => ['fo',     'Zm8',      2],
            '3 bytes' => ['foo',    'Zm9v',     3],
            '4 bytes' => ['foob',   'Zm9vYg',   4],
            '5 bytes' => ['fooba',  'Zm9vYmE',  5],
            '6 bytes' => ['foobar', 'Zm9vYmFy', 6],
        ];
    }

    /**
     * @dataProvider transformEndData
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
