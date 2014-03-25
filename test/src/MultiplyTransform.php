<?php

use Eloquent\Endec\Exception\InvalidEncodedDataException;
use Eloquent\Endec\Transform\AbstractDataTransform;

class MultiplyTransform extends AbstractDataTransform
{
    public function transform($data, $isEnd = false)
    {
        $consumedBytes = $this->calculateConsumeBytes($data, $isEnd, 2);
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
