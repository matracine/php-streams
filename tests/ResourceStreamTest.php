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

use mracine\Streams\ResourceStream;
use mracine\Streams\Exception\StreamException;

/**
 * @coversDefaultClass mracine\Streams\ResourceStream
 */
class ResourceStreamTest extends TestCase
{
    /**
     * Test that constructor throws an InvalidArgumentException on bad parameter type
     *
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     * @dataProvider constructNotResourceProvider
     */

    public function test__constructNotResource($notResource)
    {
        new ResourceStream($notResource);
    }

    /**
     * Data provider for test__constructNotResource
     *
     * returns data not of type resource
     */
    public function constructNotResourceProvider()
    {
        return [
            [0],          // integer
            [3.14],       // float
            ['a string'], // string
            [
                [ 0 , 3.14 ]   // Array
            ],
            [ new \stdClass() ], // Object
            // What else ?
        ];
    }

    /**
     * Test that constructor is OK with different kinds of resources
     *
     * @covers ::__construct
     * @dataProvider constructProvider
     * @param resource $resource Cannot typehint, PHP refuse it
     * @param bool $closeResource shall we close the resource ?
     */
    public function test_construct($resource, bool $closeResource=true)
    {
        $resourceStream = new ResourceStream($resource);

        $stream = $this->getObjectAttribute($resourceStream, 'stream');
        $this->assertInternalType(IsType::TYPE_RESOURCE, $stream);

        if ($closeResource) {
            fclose($resource);
        }
    }

    /**
     * Data provider for test__construct
     *
     * returns data of type resource
     */
    public function constructProvider()
    {
        return [
            [ fopen(__FILE__, 'r'), ], // Myself, sure I exists
            [ fsockopen('tcp://127.0.0.1', 18728),  ], // Socket
            [ STDIN, false ], // Try it, but do not close STDIN please !!!
            // What else ?
        ];
    }

    /**
     * Test that read function return expected values, and that consecutive reads return data
     *
     * @covers ::read
     * @dataProvider readProvider
     * @param resource $resource Cannot typehint, PHP refuse it
     * @param string $expected the rsult we should have
     */
    public function test__read(ResourceStream $stream, string $expected)
    {
        $this->assertSame($expected, $stream->read(strlen($expected)));
    }

    public function readProvider()
    {
        $resource = fopen(__FILE__, 'r');
        $me = new ResourceStream($resource);
        return [
            [ $me, '<'],  // Read for byte
            [ $me, '?php'], // Read following bytes. File statrts with "<php"
        ];
        fclose($resource);
    }

    /**
     * Test that invalid lengths amke read method throw InvalidArgumentException
     *
     * @covers ::read
     * @dataProvider readBadLengthProvider
     * @expectedException \InvalidArgumentException
     * @param resource $resource Cannot typehint, PHP refuse it
     */
    public function test__readBadLength(ResourceStream $stream, int $length)
    {
        $stream->read($length);
    }

    public function readBadLengthProvider()
    {
        $resource = fopen(__FILE__, 'r');
        $me = new ResourceStream($resource);
        return [
            [ $me, 0 ],
            [ $me, -1 ],
        ];
        fclose($resource);
    }

    /**
     * Test read to invalid (closed) resource
     *
     * @covers ::read
     * @dataProvider readBadResourceProvider
     * @expectedException mracine\Streams\Exception\StreamException
     * @param resource $resource Cannot typehint, PHP refuse it
     */
    public function test__readBadResource(ResourceStream $stream, int $length)
    {
        $stream->read($length);
    }

    public function readBadResourceProvider()
    {
        $resource = fopen(__FILE__, 'r');
        $me = new ResourceStream($resource);
        fclose($resource);
        return [
            [ $me, 1 ],
        ];
    }

    /**
     * Test that write method returns writen length
     *
     * @covers ::write
     * @dataProvider writeProvider
     * @param ResourceStram $resource to test
     * @param string $toWrite the writed string
     */
    public function test__write(ResourceStream $stream, string $toWrite)
    {
        $this->assertEquals(strlen($toWrite), $stream->write($toWrite));
    }

    public function writeProvider()
    {
        $resource = fopen("/dev/null", 'w');
        return [
            [ new ResourceStream($resource), 'yyaagagagag'],  // Take that
        ];
        fclose($resource);
    }

    /**
     * Test write to invalid resource
     *
     * @covers ::write
     * @dataProvider writeBadResourceProvider
     * @expectedException mracine\Streams\Exception\StreamException
     * @param resource $resource to test
     * @param string $toWrite the writed string
     */
    public function test__writeBadResource(ResourceStream $stream)
    {
        $stream->write('Hello ?');
    }

    public function writeBadResourceProvider()
    {
        // Has to be improved, on network outage, is the resource prperly closed like fclose do ?
        $resource = fopen('/dev/null', 'w');
        $me = new ResourceStream($resource);
        fclose($resource); // Make the stream invalid without advertising the Stream object (network outage ?)
        return [
            [ $me ],  // Take that
        ];
    }

    /**
     * Test double close resource
     *
     * @covers ::close
     * @dataProvider simpleWriteStreamResourceProvider
     * @expectedException mracine\Streams\Exception\StreamException
     * @param resource $resource to test
     */
    public function test_doubleClose(ResourceStream $stream)
    {
        $stream->close();
        $stream->close();
    }

    /**
     * Test write to closed resource
     *
     * @covers ::close
     * @covers ::write
     * @dataProvider simpleWriteStreamResourceProvider
     * @expectedException mracine\Streams\Exception\StreamException
     * @param resource $resource to test
     */
    public function test_closeWrite(ResourceStream $stream)
    {
        $stream->close();
        $stream->write('Toc toc ?');
    }

    /**
     * Test write to closed resource
     *
     * @covers ::close
     * @covers ::read
     * @dataProvider simpleReadStreamResourceProvider
     * @expectedException mracine\Streams\Exception\StreamException
     * @param resource $resource to test
     */
    public function test_closeRead(ResourceStream $stream)
    {
        $stream->close();
        $stream->read(1);
    }

    public function simpleWriteStreamResourceProvider()
    {
        return [
            [ new ResourceStream(fopen('/dev/null', 'w')) ],
        ];
    }

    public function simpleReadStreamResourceProvider()
    {
        return [
            [ new ResourceStream(fopen(__FILE__, 'r')) ],
        ];
    }
}
