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

use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;
use Eloquent\Endec\Transform\TransformStreamInterface;

/**
 * The interface implemented by decoders.
 */
interface DecoderInterface
{
    /**
     * Decode the supplied data.
     *
     * @param string $data The data to decode.
     *
     * @return string                      The decoded data.
     * @throws TransformExceptionInterface If the data cannot be decoded.
     */
    public function decode($data);

    /**
     * Create a new decode stream.
     *
     * @param integer|null $bufferSize The buffer size in bytes.
     *
     * @return TransformStreamInterface The newly created decode stream.
     */
    public function createDecodeStream($bufferSize = null);
}
