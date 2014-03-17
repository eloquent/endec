<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Encoding\Base64;

use Eloquent\Endec\Encoding\Exception\InvalidEncodedDataException;
use Eloquent\Endec\Transform\AbstractDataTransform;
use Eloquent\Endec\Transform\Exception\TransformExceptionInterface;

/**
 * Decodes data using base64 encoding.
 */
class Base64Decoder extends AbstractDataTransform
{
    /**
     * Transform the supplied data chunk.
     *
     * @param string  $data  The data to process.
     * @param boolean $isEnd True if all data should be consumed.
     *
     * @return tuple<string,integer>       A 2-tuple of the transformed data, and the number of bytes consumed.
     * @throws TransformExceptionInterface If the data cannot be transformed.
     */
    protected function doTransform($data, $isEnd)
    {
        $consumedBytes = $this->calculateConsumedBytes($data, $isEnd, 4);

        $consumedData = substr($data, 0, $consumedBytes);
        $outputBuffer = base64_decode($consumedData, true);
        if (false === $outputBuffer) {
            throw new InvalidEncodedDataException('base64', $consumedData);
        }

        return array($outputBuffer, $consumedBytes);
    }
}
