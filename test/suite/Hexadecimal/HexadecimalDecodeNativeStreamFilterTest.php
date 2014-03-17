<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Hexadecimal;

use Eloquent\Endec\Endec;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Hexadecimal\HexadecimalDecodeNativeStreamFilter
 * @covers \Eloquent\Endec\Transform\AbstractNativeStreamFilter
 */
class HexadecimalDecodeNativeStreamFilterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Endec::registerFilters();
    }

    public function testFilter()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $stream = fopen($path, 'wb');
        stream_filter_append($stream, 'endec.hexadecimal-decode');
        fwrite($stream, '6');
        fwrite($stream, '66f6f626172');
        fclose($stream);
        $actual = file_get_contents($path);
        unlink($path);

        $this->assertSame('foobar', $actual);
    }

    public function testFilterFailure()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $stream = fopen($path, 'wb');
        stream_filter_append($stream, 'endec.hexadecimal-decode');
        $actual = fwrite($stream, '$');
        fclose($stream);
        unlink($path);

        $this->assertSame(0, $actual);
    }
}
