<?php declare(strict_types=1);
/*
 * This file is part of mracine/php-streams.
 *
 * (c) Matthieu Racine <matthieu.racine@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace mracine\Streams;

use mracine\Streams\Stream;
use mracine\Streams\Exception\StreamException;
use mracine\Streams\Exception\TimeoutException;

/**
 * class ResourceStream
 *
 * Stream using a resource (socket, file, pipe etc.)
 *
 * @since 0.1
 */

class ResourceStream implements Stream
{
    /**
     * @var resource|null $stream null if stream has been closed
     */
    protected $stream;

    /**
     * Constructs a stream from a resource
     *
     * @since 0.1
     * @param resource $stream the PHP resource (PHP refuse resource type hinting)
     * @throws  InvalidArgumentException when $stream is not a resource
     */
    public function __construct($stream)
    {
        if (false === is_resource($stream)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument must be a valid resource type. %s given.',
                    gettype($stream)
                )
            );
        }
        // // TODO  : Should we verify the resource type ?
        $this->stream = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length) : string
    {
        if (is_null($this->stream)) {
            throw new StreamException(sprintf("Cannot read from closed (null) stream"));
        }
        if ($length<=0) {
            throw new \InvalidArgumentException("Cannot read zero ot negative count of bytes from a stream");
        }

        try {
            $result = @fread($this->stream, $length);            
        } catch(\Throwable $e) {
            throw new StreamException(sprintf("Error reading %d bytes", $length));
        }
        if (false === $result) {
            throw new StreamException(sprintf("Error reading %d bytes", $length));
        }
        
        $streamInfos = stream_get_meta_data($this->stream);
        if (true === $streamInfos['timed_out']) {
            // TODO: How to test timeout ???
            // @codeCoverageIgnoreStart
            throw new TimeoutException(sprintf("Timeout reading %d bytes", $length));
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $string, $length = null) : int
    {
        if (is_null($this->stream)) {
            throw new StreamException(sprintf("Cannot write to closed (null) stream"));
        }
        if (is_null($length)) {
            $length = strlen($string);
        }
        try {
            $result = @fwrite($this->stream, $string, $length);
        } catch (\Throwable $e) {
            throw new StreamException(sprintf("Error writing %d bytes", $length));
        }
        if (false === $result) {
            throw new StreamException(sprintf("Error writing %d bytes", $length));
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        $hasBeenClosed = false;
        if (!is_null($this->stream)) {
            $hasBeenClosed = @fclose($this->stream);
            $this->stream = null;
        }
        if (false === $hasBeenClosed) {
            throw new StreamException("Error closing stream");
        }
    }
}
