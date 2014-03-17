<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Transform;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

/**
 * The interface implemented by data transforms.
 */
interface DataTransformInterface extends
    ReadableStreamInterface,
    WritableStreamInterface
{
    /**
     * Transform the supplied data synchronously.
     *
     * @param string $data The data to transform.
     *
     * @return string                                The transformed data.
     * @throws Exception\TransformExceptionInterface If the data cannot be transformed.
     */
    public function transform($data);
}
