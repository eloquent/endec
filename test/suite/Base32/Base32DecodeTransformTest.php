<?php

/*
 * This file is part of the Endec package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Endec\Base32;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Endec\Base32\Base32DecodeTransform
 * @covers \Eloquent\Endec\Transform\AbstractDataTransform
 */
class Base32DecodeTransformTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new Base32DecodeTransform;
    }

    public function transformData()
    {
        //                      input               output        bytesConsumed
        return array(
            'Empty'    => array('',                 '',           0),
            '1 byte'   => array('M',                '',           0),
            '2 bytes'  => array('MZ',               '',           0),
            '3 bytes'  => array('MZX',              '',           0),
            '4 bytes'  => array('MZXW',             '',           0),
            '5 bytes'  => array('MZXW6',            '',           0),
            '6 bytes'  => array('MZXW6Y',           '',           0),
            '7 bytes'  => array('MZXW6YT',          '',           0),
            '8 bytes'  => array('MZXW6YTB',         'fooba',      8),
            '9 bytes'  => array('MZXW6YTBO',        'fooba',      8),
            '10 bytes' => array('MZXW6YTBOJ',       'fooba',      8),
            '11 bytes' => array('MZXW6YTBOJR',      'fooba',      8),
            '12 bytes' => array('MZXW6YTBOJRG',     'fooba',      8),
            '13 bytes' => array('MZXW6YTBOJRGC',    'fooba',      8),
            '14 bytes' => array('MZXW6YTBOJRGC6',   'fooba',      8),
            '15 bytes' => array('MZXW6YTBOJRGC6T',  'fooba',      8),
            '16 bytes' => array('MZXW6YTBOJRGC6TR', 'foobarbazq', 16),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input));
    }

    public function transformEndData()
    {
        //                                   input               output        bytesConsumed
        return array(
            'Empty'                 => array('',                 '',           0),
            '2 bytes'               => array('MY',               'f',          2),
            '4 bytes'               => array('MZXQ',             'fo',         4),
            '5 bytes'               => array('MZXW6',            'foo',        5),
            '7 bytes'               => array('MZXW6YQ',          'foob',       7),
            '8 bytes'               => array('MZXW6YTB',         'fooba',      8),
            '8 bytes with padding'  => array('MY======',         'f',          8),
            '10 bytes'              => array('MZXW6YTBOI',       'foobar',     10),
            '12 bytes'              => array('MZXW6YTBOJRA',     'foobarb',    12),
            '13 bytes'              => array('MZXW6YTBOJRGC',    'foobarba',   13),
            '15 bytes'              => array('MZXW6YTBOJRGC6Q',  'foobarbaz',  15),
            '16 bytes'              => array('MZXW6YTBOJRGC6TR', 'foobarbazq', 16),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $bytesConsumed)
    {
        $this->assertSame(array($output, $bytesConsumed), $this->transform->transform($input, true));
    }

    public function testTransformFailure()
    {
        $this->setExpectedException('Eloquent\Endec\Exception\InvalidEncodedDataException');
        $this->transform->transform('$', true);
    }

    public function testInstance()
    {
        $className = get_class($this->transform);
        Liberator::liberateClass($className)->instance = null;
        $instance = $className::instance();

        $this->assertInstanceOf($className, $instance);
        $this->assertSame($instance, $className::instance());
    }
}
