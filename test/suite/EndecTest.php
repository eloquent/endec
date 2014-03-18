<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec;

use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;
use Phake;

class EndecTest extends PHPUnit_Framework_TestCase
{
    public function testRegisterFilters()
    {
        $isolator = Phake::mock(Isolator::className());
        Endec::registerFilters($isolator);

        Phake::verify($isolator)->stream_filter_register(
            'endec.base16-encode',
            'Eloquent\Endec\Base16\Base16EncodeNativeStreamFilter'
        );
        Phake::verify($isolator)->stream_filter_register(
            'endec.base16-decode',
            'Eloquent\Endec\Base16\Base16DecodeNativeStreamFilter'
        );
        Phake::verify($isolator)->stream_filter_register(
            'endec.base64-encode',
            'Eloquent\Endec\Base64\Base64EncodeNativeStreamFilter'
        );
        Phake::verify($isolator)->stream_filter_register(
            'endec.base64-decode',
            'Eloquent\Endec\Base64\Base64DecodeNativeStreamFilter'
        );
    }
}
