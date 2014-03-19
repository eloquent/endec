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

```php
use Eloquent\Endec\Base32\Base32;

$codec = new Base32;
echo $codec->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo $codec->decode('MZXW6YTBOI======'); // outputs 'foobar'

echo Base32::instance()->encode('foobar'); // outputs 'MZXW6YTBOI======'
echo Base32::instance()->decode('MZXW6YTBOI======'); // outputs 'foobar'
```

### Stream filters

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

### [React] streams

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

```php
use Eloquent\Endec\Base32\Base32;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

$codec = new Base32;
try {
    $codec->decode('!!!!');
} catch (TransformExceptionInterface $e) {
    echo 'Unable to decode';
}
```

<!-- References -->

[React]: http://reactphp.org/

[API documentation]: http://lqnt.co/endec/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/endec/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/endec
[coverage-image]: http://img.shields.io/coveralls/eloquent/endec/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/endec
[eloquent/endec]: https://packagist.org/packages/eloquent/endec
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-0.0.0-red.svg "This project uses semantic versioning"
