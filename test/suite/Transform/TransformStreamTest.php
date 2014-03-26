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

use Eloquent\Endec\Stream\TestWritableStream;
use Exception;
use PHPUnit_Framework_TestCase;

class TransformStreamTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->transform = new TestTransform;
        $this->stream = new TransformStream($this->transform, 2);

        $self = $this;

        $this->output = '';
        $this->stream->on(
            'data',
            function ($data, $stream) use ($self) {
                $self->output .= $data;
            }
        );

        $this->endsEmitted = $this->closesEmitted = 0;
        $this->stream->on(
            'end',
            function ($codec) use ($self) {
                $self->endsEmitted++;
            }
        );
        $this->stream->on(
            'close',
            function ($codec) use ($self) {
                $self->closesEmitted++;
            }
        );
        $this->stream->on(
            'error',
            function ($error, $codec) use ($self) {
                throw $error;
            }
        );

        $this->transformClosure = function ($data, &$context, $isEnd = false) {
            $length = strlen($data);
            if ($isEnd) {
                $consumedBytes = $length;
            } else {
                $consumedBytes = $length - ($length % 2);
            }

            return array(strtoupper(substr($data, 0, $consumedBytes)), $consumedBytes);
        };

        $this->error = new Exception('You done goofed.');
        $this->errorClosure = function ($data, &$context, $isEnd = false) use ($self) {
            throw $self->error;
        };

        $this->transform->callbacks[] = $this->transformClosure;
    }

    public function testConstructor()
    {
        $this->assertSame($this->transform, $this->stream->transform());
        $this->assertSame(2, $this->stream->bufferSize());
        $this->assertTrue($this->stream->isWritable());
        $this->assertTrue($this->stream->isReadable());
    }

    public function testConstructorDefaults()
    {
        $this->stream = new TransformStream($this->transform);

        $this->assertSame(8192, $this->stream->bufferSize());
    }

    public function transformData()
    {
        $data = array();
        for ($i = 1; $i < 6; $i++) {
            $data[sprintf('%d byte(s)', $i)] = array(substr('foobar', 0, $i));
        }

        return $data;
    }

    /**
     * @dataProvider transformData
     */
    public function testWriteEnd($data)
    {
        $writeReturn = $this->stream->write($data);
        $this->stream->end();

        $this->assertTrue($writeReturn);
        $this->assertSame(strtoupper($data), $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    /**
     * @dataProvider transformData
     */
    public function testEnd($data)
    {
        $this->stream->end($data);

        $this->assertSame(strtoupper($data), $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testEndEmptyString()
    {
        $this->stream->end('');

        $this->assertSame('', $this->output);
    }

    public function testClose()
    {
        $this->stream->write('foo');
        $this->stream->close();
        $this->stream->close();
        $this->stream->end('bar');

        $this->assertFalse($this->stream->write('baz'));
        $this->assertSame('FO', $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
    }

    public function testPauseResume()
    {
        $this->stream->pause();

        $this->assertFalse($this->stream->write('f'));
        $this->assertSame('', $this->output);

        $this->stream->resume();

        $this->assertTrue($this->stream->write('ooba'));
        $this->assertSame('FOOB', $this->output);

        $this->stream->end('r');

        $this->assertSame('FOOBAR', $this->output);
    }

    public function testPipe()
    {
        $destination = new TestWritableStream;
        $this->stream->pipe($destination);
        $this->stream->end('foobar');

        $this->assertSame('FOOBAR', $destination->data);
    }

    public function testTransformFailure()
    {
        $self = $this;
        $this->errorsEmitted = array();
        $this->stream->removeAllListeners('error');
        $this->stream->on(
            'error',
            function ($error, $codec) use ($self) {
                $self->errorsEmitted[] = $error;
            }
        );
        $this->transform->callbacks = array($this->transformClosure, $this->errorClosure, $this->transformClosure);
        $writeReturn = $this->stream->write('foo');

        $this->assertTrue($writeReturn);
        $this->assertSame('FO', $this->output);
        $this->assertSame(0, $this->endsEmitted);
        $this->assertSame(0, $this->closesEmitted);
        $this->assertSame(array(), $this->errorsEmitted);

        $writeReturn = $this->stream->write('bar');

        $this->assertFalse($writeReturn);
        $this->assertSame('FO', $this->output);
        $this->assertSame(0, $this->endsEmitted);
        $this->assertSame(0, $this->closesEmitted);
        $this->assertSame(array($this->error), $this->errorsEmitted);

        $this->stream->end();

        $this->assertFalse($writeReturn);
        $this->assertSame('FOOBAR', $this->output);
        $this->assertSame(1, $this->endsEmitted);
        $this->assertSame(1, $this->closesEmitted);
        $this->assertSame(array($this->error), $this->errorsEmitted);
    }
}
