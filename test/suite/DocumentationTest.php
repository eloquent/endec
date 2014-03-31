<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eloquent\Endec\Base32\Base32;
use Eloquent\Endec\Codec;
use Eloquent\Endec\Decoder;
use Eloquent\Endec\Encoder;
use Eloquent\Endec\Endec;
use Eloquent\Endec\Exception\EncodingExceptionInterface;

class DocumentationTest extends PHPUnit_Framework_TestCase
{
    public function testStringUsage()
    {
        $this->expectOutputString('MZXW6YTBOI======foobarMZXW6YTBOI======foobar');

$codec = new Base32;
echo $codec->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo $codec->decode('MZXW6YTBOI======'); // outputs 'foobar'

echo Base32::instance()->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo Base32::instance()->decode('MZXW6YTBOI======'); // outputs 'foobar'
    }

    // =========================================================================

    public function testStreamFilterUsage()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $this->expectOutputString('MZXW6YTBOI======foobar');

Endec::registerFilters();
// $path = '/path/to/file';

$stream = fopen($path, 'wb');
stream_filter_append($stream, 'endec.base32-encode');
fwrite($stream, 'fo');
fwrite($stream, 'ob');
fwrite($stream, 'ar');
fclose($stream);
echo file_get_contents($path); // outputs 'MZXW6YTBOI======'

$stream = fopen($path, 'rb');
stream_filter_append($stream, 'endec.base32-decode');
$data = fread($stream, 3);
$data .= fread($stream, 3);
$data .= fread($stream, 2);
fclose($stream);
echo $data; // outputs 'foobar'

        unlink($path);
    }

    // =========================================================================

    public function testReactStreamUsage()
    {
        $this->expectOutputString('MZXW6YTBOI======foobar');

$codec = new Base32;
$encodeStream = $codec->createEncodeStream();
$decodeStream = $codec->createDecodeStream();

$encoded = '';
$encodeStream->on(
    'data',
    function ($data, $stream) use (&$encoded) {
        $encoded .= $data;
    }
);

$decoded = '';
$decodeStream->on(
    'data',
    function ($data, $stream) use (&$decoded) {
        $decoded .= $data;
    }
);

$encodeStream->pipe($decodeStream);

$encodeStream->write('fo');
$encodeStream->write('ob');
$encodeStream->end('ar');

echo $encoded; // outputs 'MZXW6YTBOI======'
echo $decoded; // outputs 'foobar'
    }

    // =========================================================================

    public function testHandlingErrorsStringUsage()
    {
        $this->expectOutputString('Unable to decode');

$codec = new Base32;
try {
    $codec->decode('!!!!!!!!');
} catch (EncodingExceptionInterface $e) {
    echo 'Unable to decode';
}
    }

    // =========================================================================

    public function testHandlingErrorsStreamFilterUsage()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $this->expectOutputString('Unable to decode');

Endec::registerFilters();
// $path = '/path/to/file';

$stream = fopen($path, 'wb');
stream_filter_append($stream, 'endec.base32-decode');
if (!fwrite($stream, '!!!!!!!!')) {
    echo 'Unable to decode';
}
fclose($stream);
    }

    // =========================================================================

    public function testHandlingErrorsReactStreamUsage()
    {
        $path = tempnam(sys_get_temp_dir(), 'endec');
        $this->expectOutputString('Unable to decode');

$codec = new Base32;
$decodeStream = $codec->createDecodeStream();

$decodeStream->on(
    'error',
    function ($error, $stream) {
        echo 'Unable to decode';
    }
);

$decodeStream->end('!!!!!!!!');
    }

    // =========================================================================

    public function testCustomEncodingExamples()
    {
        $this->expectOutputString('sbbonesbbonefoobar');

$transform = new Rot13Transform;

$encoder = new Encoder($transform);
echo $encoder->encode('foobar'); // outputs 'sbbone'

$decoder = new Decoder($transform);
echo $decoder->decode('foobar'); // outputs 'sbbone'

$codec = new Codec($transform, $transform);
echo $codec->decode($codec->encode('foobar')); // outputs 'foobar'
    }
}
