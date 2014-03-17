<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class InvalidEncodedDataExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $cause = new Exception;
        $exception = new InvalidEncodedDataException('encodingName', 'data', $cause);

        $this->assertSame('encodingName', $exception->encoding());
        $this->assertSame('data', $exception->data());
        $this->assertSame('The supplied data is not valid for encodingName encoding.', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
