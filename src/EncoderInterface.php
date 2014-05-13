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

use Eloquent\Confetti\TransformStreamInterface;

/**
 * The interface implemented by encoders.
 */
interface EncoderInterface
{
    /**
     * Encode the supplied data.
     *
     * @param string $data The data to encode.
     *
     * @return string                               The encoded data.
     * @throws Exception\EncodingExceptionInterface If the data cannot be encoded.
     */
    public function encode($data);

    /**
     * Create a new encode stream.
     *
     * @param integer|null $bufferSize The buffer size in bytes.
     *
     * @return TransformStreamInterface The newly created encode stream.
     */
    public function createEncodeStream($bufferSize = null);
}
