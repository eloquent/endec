<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eloquent\Endec\Base64\Base64;
use Eloquent\Endec\Endec;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

class DocumentationTest extends PHPUnit_Framework_TestCase
{
    public function testStringUsage()
    {
        $this->expectOutputString('Zm9vYmFyfoobarZm9vYmFyfoobar');

        $codec = new Base64;
        echo $codec->encode('foobar'); // outputs 'Zm9vYmFy'
        echo $codec->decode('Zm9vYmFy'); // outputs 'foobar'

        echo Base64::instance()->encode('foobar'); // outputs 'Zm9vYmFy'
        echo Base64::instance()->decode('Zm9vYmFy'); // outputs 'foobar'
    }

    public function testStreamFilterUsage()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $this->expectOutputString('Zm9vYmFyfoobar');

        Endec::registerFilters();
        // $path = '/path/to/file';

        $stream = fopen($path, 'wb');
        stream_filter_append($stream, 'endec.base64-encode');
        fwrite($stream, 'fo');
        fwrite($stream, 'ob');
        fwrite($stream, 'ar');
        fclose($stream);
        echo file_get_contents($path); // outputs 'Zm9vYmFy'

        $stream = fopen($path, 'rb');
        stream_filter_append($stream, 'endec.base64-decode');
        $data = fread($stream, 3);
        $data .= fread($stream, 3);
        $data .= fread($stream, 2);
        fclose($stream);
        echo $data; // outputs 'foobar'

        unlink($path);
    }

    public function testReactStreamUsage()
    {
        $this->expectOutputString('Zm9vYmFyfoobar');

        $codec = new Base64;
        $encodeStream = $codec->createEncodeStream();
        $decodeStream = $codec->createDecodeStream();

        $encoded = '';
        $encodeStream->on(
            'data',
            function ($data, $codec) use (&$encoded) {
                $encoded .= $data;
            }
        );

        $decoded = '';
        $decodeStream->on(
            'data',
            function ($data, $codec) use (&$decoded) {
                $decoded .= $data;
            }
        );

        $encodeStream->pipe($decodeStream);

        $encodeStream->write('fo');
        $encodeStream->write('ob');
        $encodeStream->end('ar');

        echo $encoded; // outputs 'Zm9vYmFy'
        echo $decoded; // outputs 'foobar'
    }

    public function testHandlingErrorsUsage()
    {
        $this->expectOutputString('Unable to decode');

        $codec = new Base64;
        try {
            $codec->decode('!!!!');
        } catch (TransformExceptionInterface $e) {
            echo 'Unable to decode';
        }
    }
}
