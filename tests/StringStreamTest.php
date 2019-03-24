<?php declare(strict_types=1);
/*
 * This file is part of mracine/php-streams.
 *
 * (c) Matthieu Racine <matthieu.racine@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace mracine\Streams\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsType;

use mracine\Streams\StringStream;
use mracine\Streams\Exceptions\StreamException;

/**
 * @coversDefaultClass mracine\Streams\StringStream
 */
class StringStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @dataProvider constructProvider
     */
    public function test__construct(string $string)
    {
        $this->assertInstanceOf(StringStream::class, new StringStream($string));
    }

    public function constructProvider()
    {
        return [
            [ chr(0) ],
            [ ''     ],
            [ '1'    ],
            [ 'lkjl'.chr(0).'kjkljllkjkljljklkjkljlkjljlkjkljkljlkjjll'],
        ];
    }


    /**
     * test that write function returns the effective writen bytes
     * @covers ::write
     * @dataProvider writeProvider
     * @param string $toWrite the string to write
     * @param int|null $length the count if bytes to write
     * @param int $expected the number of bytes that must be writen
     */

    public function test__write(string $string, $length, int $expected)
    {
        $stream = new StringStream('Does not matters');
        if (is_null($length)) {
            $this->assertEquals($expected, $stream->write($string));
        } else {
            $this->assertEquals($expected, $stream->write($string, $length));
        }
    }

    public function writeProvider()
    {
        return [
            [ '',  0, 0 ],
            [ '', 10, 0 ],
            [ '', null, 0 ],
            [ 'Yabala', 0, 0],
            [ 'Yabala', 1, 1],
            [ 'Yabala', 6, 6],
            [ 'Yabala', 100, 6],
            [ 'Yabala', null, 6],
            [ chr(0), 0, 0],
            [ chr(0), 1, 1],
            [ chr(0), 100, 1],
            [ chr(0), null, 1],
        ];
    }

    /**
     * @covers ::write
     * @expectedException \InvalidArgumentException
     */
    public function test__writeWithNegativeLength()
    {
        $stream = new StringStream('Does not matters');
        $stream->write("PLOP", -1);
    }

    /**
     * @covers ::write
     * @expectedException mracine\Streams\Exception\StreamException
     */
    public function test__writeClosed()
    {
        $stream = new StringStream('Does not matters');
        $stream->close();
        $stream->write("PLOP");
    }


    /**
     * Test read function
     */
    public function test__read()
    {
        $stream = new StringStream('123456789');

        $this->assertEquals('', $stream->read(0));
        $this->assertEquals('1', $stream->read(1));
        $this->assertEquals('23', $stream->read(2));
        $this->assertEquals('456', $stream->read(3));
        $this->assertEquals('', $stream->read(0));
        $this->assertEquals('789', $stream->read(4));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test__readBadLength()
    {
        $stream = new StringStream('123456789');
        $stream->read(-1);
    }

    /**
     * @covers ::read
     * @dataProvider readWhileEmptyProvider
     * @expectedException mracine\Streams\Exception\StreamException
     */
    public function test__readWhileEmpty(StringStream $stream, int $length)
    {
        $stream->read($length);
    }

    public function readWhileEmptyProvider()
    {
        $stream = new StringStream('123456789');
        $stream->read(9);
        yield [$stream, 1];

        $stream = new StringStream('123456789');
        $stream->read(5);
        $stream->read(4);
        yield [$stream, 1];

        $stream = new StringStream('');
        yield [$stream, 1];
    }

    /**
     * @expectedException mracine\Streams\Exception\StreamException
     */
    public function testReadClosed()
    {
        $stream = new StringStream('123456789');
        $stream->close();
        $stream->read(1);
    }
}
