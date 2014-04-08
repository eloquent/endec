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
 * @covers \Eloquent\Endec\Base32\AbstractBase32DecodeTransform
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
        //                                   input               output        consumed context
        return array(
            'Empty'                 => array('',                 '',           0,            null),
            '1 byte'                => array('M',                '',           0,            null),
            '2 bytes'               => array('MZ',               '',           0,            null),
            '3 bytes'               => array('MZX',              '',           0,            null),
            '4 bytes'               => array('MZXW',             '',           0,            null),
            '5 bytes'               => array('MZXW6',            '',           0,            null),
            '6 bytes'               => array('MZXW6Y',           '',           0,            null),
            '7 bytes'               => array('MZXW6YT',          '',           0,            null),
            '8 bytes'               => array('MZXW6YTB',         'fooba',      8,            null),
            '9 bytes'               => array('MZXW6YTBO',        'fooba',      8,            null),
            '10 bytes'              => array('MZXW6YTBOJ',       'fooba',      8,            null),
            '11 bytes'              => array('MZXW6YTBOJR',      'fooba',      8,            null),
            '12 bytes'              => array('MZXW6YTBOJRG',     'fooba',      8,            null),
            '13 bytes'              => array('MZXW6YTBOJRGC',    'fooba',      8,            null),
            '14 bytes'              => array('MZXW6YTBOJRGC6',   'fooba',      8,            null),
            '15 bytes'              => array('MZXW6YTBOJRGC6T',  'fooba',      8,            null),
            '16 bytes'              => array('MZXW6YTBOJRGC6TR', 'foobarbazq', 16,           null),
            '16 bytes with padding' => array('MZXW6YTBOI======', 'foobar',     16,           null),
        );
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext));
        $this->assertSame($context, $actualContext);
    }

    public function transformEndData()
    {
        //                                   input               output        consumed context
        return array(
            'Empty'                 => array('',                 '',           0,            null),
            '2 bytes'               => array('MY',               'f',          2,            null),
            '4 bytes'               => array('MZXQ',             'fo',         4,            null),
            '5 bytes'               => array('MZXW6',            'foo',        5,            null),
            '7 bytes'               => array('MZXW6YQ',          'foob',       7,            null),
            '8 bytes'               => array('MZXW6YTB',         'fooba',      8,            null),
            '8 bytes with padding'  => array('MY======',         'f',          8,            null),
            '10 bytes'              => array('MZXW6YTBOI',       'foobar',     10,           null),
            '12 bytes'              => array('MZXW6YTBOJRA',     'foobarb',    12,           null),
            '13 bytes'              => array('MZXW6YTBOJRGC',    'foobarba',   13,           null),
            '15 bytes'              => array('MZXW6YTBOJRGC6Q',  'foobarbaz',  15,           null),
            '16 bytes'              => array('MZXW6YTBOJRGC6TR', 'foobarbazq', 16,           null),
            '16 bytes with padding' => array('MZXW6YTBOI======', 'foobar',     16,           null),
        );
    }

    /**
     * @dataProvider transformEndData
     */
    public function testTransformEnd($input, $output, $consumed, $context)
    {
        $this->assertSame(array($output, $consumed, null), $this->transform->transform($input, $actualContext, true));
        $this->assertSame($context, $actualContext);
    }

    public function invalidTransformEndData()
    {
        //                                    input
        return array(
            'Characters below range' => array('!!!!!!!!'),
            'Characters above range' => array('~~~~~~~~'),
            '1 byte'                 => array('A'),
            '9 bytes'                => array('AAAAAAAAA'),
        );
    }

    /**
     * @dataProvider invalidTransformEndData
     */
    public function testTransformFailure($input)
    {
        list($output, $consumed, $error) = $this->transform->transform($input, $context, true);

        $this->assertSame('', $output);
        $this->assertSame(0, $consumed);
        $this->setExpectedException(
            'Eloquent\Endec\Exception\InvalidEncodedDataException',
            'The supplied data is not valid for base32 encoding.'
        );
        throw $error;
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
