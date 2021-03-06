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

use Eloquent\Endec\Endec;
use PHPUnit_Framework_TestCase;

class Base32DecodeNativeStreamFilterTest extends PHPUnit_Framework_TestCase
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
        stream_filter_append($stream, 'endec.base32-decode');
        fwrite($stream, 'M');
        fwrite($stream, 'ZXW6YTBOI======');
        fclose($stream);
        $actual = file_get_contents($path);
        unlink($path);

        $this->assertSame('foobar', $actual);
    }

    public function testFilterFailure()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $stream = fopen($path, 'wb');
        stream_filter_append($stream, 'endec.base32-decode');
        $actual = fwrite($stream, '$');
        fclose($stream);
        unlink($path);

        $this->assertSame(0, $actual);
    }
}
