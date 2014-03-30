<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eloquent\Confetti\AbstractTransform;
use Eloquent\Endec\Exception\InvalidEncodedDataException;

class MultiplyTransform extends AbstractTransform
{
    public function transform($data, &$context, $isEnd = false)
    {
        $consumedBytes = $this->blocksSize(strlen($data), 2, $isEnd);
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
