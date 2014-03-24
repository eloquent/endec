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

use Eloquent\Endec\Endec;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base64\Base64MimeEncodeNativeStreamFilter
 * @covers \Eloquent\Endec\Transform\AbstractNativeStreamFilter
 */
class Base64MimeEncodeNativeStreamFilterTest extends PHPUnit_Framework_TestCase
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
        stream_filter_append($stream, 'endec.base64mime-encode');
        fwrite($stream, 'a');
        fwrite($stream, str_repeat('a', 63));
        fclose($stream);
        $actual = file_get_contents($path);
        unlink($path);

        $this->assertSame(
            "YWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFh\r\nYWFhYWFhYQ==\r\n",
            $actual
        );
    }
}
