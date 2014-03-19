# Endec

*Versatile encoding for PHP.*

[![There is no current stable version][version-image]][Semantic versioning]
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
with [stream_filter_remove].

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

Streams can be obtained from an [encoder, decoder, or codec]. *Endec*'s streams
implement both [WritableStreamInterface] and [ReadableStreamInterface] from the
[React] library, and hence can be used in an asynchronous manner.

```php
use Eloquent\Endec\Base32\Base32;

$codec = new Base32;
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

echo $encoded; // outputs 'MZXW6YTBOI======'
echo $decoded; // outputs 'foobar'
```

### Handling errors

Handling errors when dealing with strings is as simple as catching an exception.
All exceptions thrown implement [TransformExceptionInterface]:

```php
use Eloquent\Endec\Base32\Base32;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

$codec = new Base32;
try {
    $codec->decode('!!!!!!!!');
} catch (TransformExceptionInterface $e) {
    echo 'Unable to decode';
}
```

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

At the heart of *Endec* lies the [DataTransformInterface] interface. A correctly
implemented data transform allows *Endec* to utilize its logic for both
string-based, and streaming transformations.

A simple data transform might look like the following:

```php
use Eloquent\Endec\Transform\DataTransformInterface;

class Rot13Transform implements DataTransformInterface
{
    public function transform($data, $isEnd = false)
    {
        return array(str_rot13($data), strlen($data));
    }
}
```

The transform receives an arbitrary amount of data as a string, and returns an
array where the first element is the transformed data, and the second element is
the amount of data consumed in bytes. In this example, the data is always
completely consumed.

This transform can now be utilized like so:

```php
use Eloquent\Endec\Encoder;

$encoder = new Encoder(new Rot13Transform);
echo $encoder->encode('foobar'); // outputs 'sbbone'
```

Since rot13 is reciprocal, it could also be used to create a full codec:

```php
use Eloquent\Endec\Codec;

$transform = new Rot13Transform;
$codec = new Codec($transform, $transform);
echo $codec->encode('foobar'); // outputs 'sbbone'
echo $codec->decode('sbbone'); // outputs 'foobar'
```

More complex encodings may not be able to consume data byte-by-byte. As an
example, attempting to base64 encode each byte as it is received would result in
invalid output filled with padding characters. Data transforms also have to deal
with error conditions, such as attempting to decode invalid data.

A more complex transform might be implemented like so:

```php
use Eloquent\Endec\Exception\InvalidEncodedDataException;
use Eloquent\Endec\Transform\AbstractDataTransform;

class MultiplyTransform extends AbstractDataTransform
{
    public function transform($data, $isEnd = false)
    {
        $consumedBytes = $this->calculateConsumeBytes($data, $isEnd, 2);
        if (!$consumedBytes) {
            return array('', 0);
        }

        $consumedData = substr($data, 0, $consumedBytes);
        if (0 !== $consumedBytes % 2 || !ctype_digit($consumedData)) {
            throw new InvalidEncodedDataException('multiply', $consumedData);
        }

        $output = '';
        for ($i = 0; $i < $consumedBytes; $i += 2) {
            $output .= $consumedData[$i] * $consumedData[$i + 1] . '|';
        }

        return array($output, $consumedBytes);
    }
}
```

This transform will now multiply pairs of numbers and append the result to the
output buffer. The call to `AbstractDataTransform::calculateConsumeBytes()`
ensures that data is only consumed in blocks of 2 bytes at a time. If a
non-digit character is passed, or the data stream ends at an odd number of
bytes, an exception is thrown to indicate the error.

When used in an encoder, this transform functions like so:

```php
use Eloquent\Endec\Encoder;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

$encoder = new Encoder(new MultiplyTransform);
echo $encoder->encode('0123456789'); // outputs '0|6|20|42|72|'

try {
    $encoder->encode('foobar');
} catch (TransformExceptionInterface $e) {
    echo 'Unable to encode non-digits';
}

try {
    $encoder->encode('123');
} catch (TransformExceptionInterface $e) {
    echo 'Unable to encode odd lengths';
}
```

<!-- References -->

[CodecInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/CodecInterface.html
[DataTransformInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/Transform/DataTransformInterface.html
[DecoderInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/DecoderInterface.html
[encoder, decoder, or codec]: #encoders-decoders-and-codecs
[EncoderInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/EncoderInterface.html
[React]: http://reactphp.org/
[ReadableStreamInterface]: https://github.com/reactphp/react/blob/v0.4.0/src/Stream/ReadableStreamInterface.php
[stream filters]: http://php.net/stream.filters
[stream_filter_append]: http://php.net/stream_filter_append
[stream_filter_prepend]: http://php.net/stream_filter_prepend
[stream_filter_remove]: http://php.net/stream_filter_remove
[TransformExceptionInterface]: http://lqnt.co/endec/artifacts/documentation/api/Eloquent/Endec/Transform/Exception/TransformExceptionInterface.html
[WritableStreamInterface]: https://github.com/reactphp/react/blob/v0.4.0/src/Stream/WritableStreamInterface.php

[API documentation]: http://lqnt.co/endec/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/endec/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/endec
[coverage-image]: http://img.shields.io/coveralls/eloquent/endec/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/endec
[eloquent/endec]: https://packagist.org/packages/eloquent/endec
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-0.0.0-red.svg "This project uses semantic versioning"