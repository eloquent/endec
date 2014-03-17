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
 * The interface implemented by transform stream wrappers.
 */
interface TransformStreamInterface extends
    ReadableStreamInterface,
    WritableStreamInterface
{
    /**
     * Get the transform.
     *
     * @return DataTransformInterface The transform.
     */
    public function transform();
}
