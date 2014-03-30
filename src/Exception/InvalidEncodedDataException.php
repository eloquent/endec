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

/**
 * The supplied data is not correctly encoded.
 */
final class InvalidEncodedDataException extends Exception implements
    EncodingExceptionInterface
{
    /**
     * Construct a new invalid encoded data exception.
     *
     * @param string         $encoding The expected encoding.
     * @param string|null    $data     The invalid data, if available.
     * @param Exception|null $cause    The cause, if available.
     */
    public function __construct(
        $encoding,
        $data = null,
        Exception $cause = null
    ) {
        $this->encoding = $encoding;
        $this->data = $data;

        parent::__construct(
            sprintf(
                'The supplied data is not valid for %s encoding.',
                $encoding
            ),
            0,
            $cause
        );
    }

    /**
     * Get the name of the expected encoding.
     *
     * @return string The expected encoding.
     */
    public function encoding()
    {
        return $this->encoding;
    }

    /**
     * Get the invalid data.
     *
     * @return string|null The invalid data, if available.
     */
    public function data()
    {
        return $this->data;
    }

    private $encoding;
    private $data;
}
