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

/**
 * A static utility class for registering native stream filters.
 */
abstract class Endec
{
    /**
     * Register Endec's native stream filters.
     *
     * @param Isolator|null $isolator The isolator to use.
     */
    public static function registerFilters(Isolator $isolator = null)
    {
        $isolator = Isolator::get($isolator);

        $isolator->stream_filter_register(
            'endec.base16-encode',
            'Eloquent\Endec\Base16\Base16EncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base16-decode',
            'Eloquent\Endec\Base16\Base16DecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base32-encode',
            'Eloquent\Endec\Base32\Base32EncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base32-decode',
            'Eloquent\Endec\Base32\Base32DecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base32hex-encode',
            'Eloquent\Endec\Base32\Base32HexEncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base32hex-decode',
            'Eloquent\Endec\Base32\Base32HexDecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64-encode',
            'Eloquent\Endec\Base64\Base64EncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64-decode',
            'Eloquent\Endec\Base64\Base64DecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64mime-encode',
            'Eloquent\Endec\Base64\Base64MimeEncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64mime-decode',
            'Eloquent\Endec\Base64\Base64MimeDecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64url-encode',
            'Eloquent\Endec\Base64\Base64UrlEncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.base64url-decode',
            'Eloquent\Endec\Base64\Base64UrlDecodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.uri-encode',
            'Eloquent\Endec\Uri\UriEncodeNativeStreamFilter'
        );
        $isolator->stream_filter_register(
            'endec.uri-decode',
            'Eloquent\Endec\Uri\UriDecodeNativeStreamFilter'
        );
    }
}
