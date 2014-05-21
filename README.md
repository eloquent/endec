# Endec

*Versatile encoding implementations for PHP.*

[![The most recent stable version is 0.2.1][version-image]][Semantic versioning]
[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## Installation and documentation

* Available as [Composer] package [eloquent/endec].
* [API documentation] available.

## What is Endec?

*Endec* is a general-purpose encoding library for PHP that supports encoding and
decoding of streaming data in addition to regular string-based methods. *Endec*
comes with a selection of common encodings, is easy to use, and is simple to
extend with custom encodings if necessary.

## Usage

### Strings

*Endec* can work with strings, similar to encoding functions in the PHP standard
library. All codec classes have a static `instance()` method as a convenience
only (they are not singletons).

```php
use Eloquent\Endec\Base32\Base32;

$codec = new Base32;
echo $codec->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo $codec->decode('MZXW6YTBOI======'); // outputs 'foobar'

echo Base32::instance()->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo Base32::instance()->decode('MZXW6YTBOI======'); // outputs 'foobar'
```

### Stream filters

PHP natively supports [stream filters]. Any number of filters can be added to
any stream with [stream_filter_append] or [stream_filter_prepend], and removed
with [stream_filter_remove]. All of *Endec*'s encodings are available as [stream
filters](#built-in-stream-filters).

```php
use Eloquent\Endec\Endec;

Endec::registerFilters();
$path = '/path/to/file';

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
```

### React streams

Streams can be obtained from an [encoder, decoder, or
codec](#encoders-decoders-and-codecs). *Endec*'s streams implement both
[WritableStreamInterface] and [ReadableStreamInterface] from the [React]
library, and hence can be used in an asynchronous manner.

```php
use Eloquent\Endec\Base32\Base32;

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
```

### Handling errors

Handling errors when dealing with strings is as simple as catching an exception.
All exceptions thrown implement [TransformExceptionInterface]:

```php
use Eloquent\Endec\Base32\Base32;
use Eloquent\Endec\Exception\EncodingExceptionInterface;

$codec = new Base32;
try {
    $codec->decode('!!!!!!!!');
} catch (EncodingExceptionInterface $e) {
    echo 'Unable to decode';
}
```

When using stream filters, error handling is difficult because PHP seems to
simply [ignore errors produced by the filter]. If 0 bytes are written by
`fwrite()`, it's a fair indication that an error occurred:

```php
use Eloquent\Endec\Endec;

Endec::registerFilters();
$path = '/path/to/file';

$stream = fopen($path, 'wb');
stream_filter_append($stream, 'endec.base32-decode');
if (!fwrite($stream, '!!!!!!!!')) {
    echo 'Unable to decode';
}
fclose($stream);
```

When using [React] streams, simply handle the `error` event:

```php
use Eloquent\Endec\Base32\Base32;

$codec = new Base32;
$decodeStream = $codec->createDecodeStream();

$decodeStream->on(
    'error',
    function ($error, $stream) {
        echo 'Unable to decode';
    }
);

$decodeStream->end('!!!!!!!!');
```

## Built-in encodings

*Endec* supports a number of common encodings out of the box. Where there is a
relevant specification document, *Endec* aims to be 100% spec-conformant.
Available encodings include:

- [Base64] from [RFC 4648]
- [Base64 for MIME message bodies] from [RFC 2045]
- [Base64 with URL and filename safe alphabet] from [RFC 4648]
- [Base32] from [RFC 4648]
- [Base32 with extended hexadecimal alphabet] from [RFC 4648]
- [Base16 (hexadecimal)] from [RFC 4648]
- [URI percent encoding] from [RFC 3986]

## Built-in stream filters

All *Endec* encodings are available as stream filters. Filters must be
registered globally before use by calling `Endec::registerFilters()` (it is safe
to call this method multiple times). Available stream filters include:

- endec.base64-encode
- endec.base64-decode
- endec.base64mime-encode (see also PHP's [convert.base64-encode])
- endec.base64mime-decode (see also PHP's [convert.base64-decode])
- endec.base64url-encode
- endec.base64url-decode
- endec.base32-encode
- endec.base32-decode
- endec.base32hex-encode
- endec.base32hex-decode
- endec.base16-encode
- endec.base16-decode
- endec.uri-encode
- endec.uri-decode

## Encoders, decoders, and codecs

Most of the functionality of *Endec* is provided through *encoders*, *decoders*,
and *codecs* (codecs are simply a combination of an encoder and decoder). All of
*Endec*'s built-in encodings are implemented as codecs, but it is also possible
to implement a standalone encoder or decoder.

All encoders implement [EncoderInterface], all decoders implement
[DecoderInterface], and all codecs implement [EncoderInterface],
[DecoderInterface], and [CodecInterface]. This allows for type hints to
accurately express requirements.

## Implementing a custom encoding

Encoders and decoders are built upon [Confetti] transforms. For details on how
to implement a transform, see the Confetti documentation for [implementing a
transform].

As an example, given the following transform:

```php
use Eloquent\Confetti\TransformInterface;

class Rot13Transform implements TransformInterface
{
    public function transform($data, &$context, $isEnd = false)
    {
        return array(str_rot13($data), strlen($data));
    }
}
```

It is extremely simple to create a related encoder, decoder, or codec:

```php
use Eloquent\Endec\Codec;
use Eloquent\Endec\Decoder;
use Eloquent\Endec\Encoder;

$transform = new Rot13Transform;

$encoder = new Encoder($transform);
echo $encoder->encode('foobar'); // outputs 'sbbone'

$decoder = new Decoder($transform);
echo $decoder->decode('foobar'); // outputs 'sbbone'

$codec = new Codec($transform, $transform);
echo $codec->decode($codec->encode('foobar')); // outputs 'foobar'
```

Note that the codec takes the same tranform for encoding and decoding only
because rot13 is reciprocal. Most codecs would require a separate encode and
decode transform.

<!-- References -->

[Base16 (hexadecimal)]: http://tools.ietf.org/html/rfc4648#section-8
[Base32 with extended hexadecimal alphabet]: http://tools.ietf.org/html/rfc4648#section-7
[Base32]: http://tools.ietf.org/html/rfc4648#section-6
[Base64 for MIME message bodies]: http://tools.ietf.org/html/rfc2045#section-6.8
[Base64 with URL and filename safe alphabet]: http://tools.ietf.org/html/rfc4648#section-5
[Base64]: http://tools.ietf.org/html/rfc4648#section-4
[CodecInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/CodecInterface.html
[Confetti]: https://github.com/eloquent/confetti
[convert.base64-decode]: http://php.net/filters.convert
[convert.base64-encode]: http://php.net/filters.convert
[DataTransformInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/Transform/DataTransformInterface.html
[DecoderInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/DecoderInterface.html
[EncoderInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/EncoderInterface.html
[ignore errors produced by the filter]: https://bugs.php.net/bug.php?id=66932
[implementing a transform]: https://github.com/eloquent/confetti#implementing-a-transform
[React]: http://reactphp.org/
[ReadableStreamInterface]: https://github.com/reactphp/react/blob/v0.4.0/src/Stream/ReadableStreamInterface.php
[RFC 2045]: http://tools.ietf.org/html/rfc2045
[RFC 3986]: http://tools.ietf.org/html/rfc3986
[RFC 4648]: http://tools.ietf.org/html/rfc4648
[stream filters]: http://php.net/stream.filters
[stream_filter_append]: http://php.net/stream_filter_append
[stream_filter_prepend]: http://php.net/stream_filter_prepend
[stream_filter_remove]: http://php.net/stream_filter_remove
[TransformExceptionInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/Transform/Exception/TransformExceptionInterface.html
[URI percent encoding]: http://tools.ietf.org/html/rfc3986#section-2.1
[WritableStreamInterface]: https://github.com/reactphp/react/blob/v0.4.0/src/Stream/WritableStreamInterface.php

[API documentation]: http://lqnt.co/endec/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/endec/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/endec
[coverage-image]: http://img.shields.io/coveralls/eloquent/endec/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/endec
[eloquent/endec]: https://packagist.org/packages/eloquent/endec
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-0.2.1-yellow.svg "This project uses semantic versioning"
