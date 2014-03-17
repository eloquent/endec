<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\TestCase;

use PHPUnit_Framework_TestCase;

abstract class AbstractDataTransformTestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->output = '';
        $this->codec->on(
            'data',
            function ($data, $codec) {
                $this->output .= $data;
            }
        );

        $this->endEmitted = $this->closeEmitted = false;
        $this->codec->on(
            'end',
            function ($codec) {
                $this->endEmitted = true;
            }
        );
        $this->codec->on(
            'close',
            function ($codec) {
                $this->closeEmitted = true;
            }
        );
    }

    public function encodingData()
    {
        $data = array();
        for ($i = 1; $i < 16; $i++) {
            $data[sprintf('%d byte(s)', $i)] = array(substr('foobarbazquxdoom', 0, $i));
        }

        return $data;
    }
}
