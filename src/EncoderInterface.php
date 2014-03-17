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
     * @return string                      The encoded data.
     * @throws TransformExceptionInterface If the data cannot be encoded.
     */
    public function encode($data);
}
