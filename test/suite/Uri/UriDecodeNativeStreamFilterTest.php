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

use Eloquent\Endec\Endec;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Uri\UriDecodeNativeStreamFilter
 * @covers \Eloquent\Endec\Transform\AbstractNativeStreamFilter
 */
class UriDecodeNativeStreamFilterTest extends PHPUnit_Framework_TestCase
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
        stream_filter_append($stream, 'endec.uri-decode');
        fwrite($stream, 'f%');
        fwrite($stream, '21o%40o%23');
        fclose($stream);
        $actual = file_get_contents($path);
        unlink($path);

        $this->assertSame('f!o@o#', $actual);
    }
}
