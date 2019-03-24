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

/**
 * class StringStream
 *
 * Initialized with a string, the read method retreive it as done with fread, consuming the buffer.
 * When all the string has been read, exception is thrown when try to read again.
 *
 * @since   0.1
 */

class StringStream implements Stream
{
    /**
     * @var string $buffer Stores the string to use
     */
    protected $buffer;

    /**
     * Constuctor
     *
     * @param string $string the initial data accessible via read method
     */
    public function __construct(string $string)
    {
        $this->buffer = $string;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length) : string
    {
        if (is_null($this->buffer)) {
            throw new StreamException(sprintf("Cannot read from closed (null) stream"));
        }

        $remaining = strlen($this->buffer);

        if ($length<0) {
            throw new \InvalidArgumentException("Cannot read a negative count of bytes from a stream");
        }

        if (0 == $remaining) {
            throw new StreamException("End of stream");
        }

        if ($length>=$remaining) {
            // returns all
            $result = $this->buffer;
            // No more in the buffer
            $this->buffer='';
        } else {
            // acquire $length characters from the buffer
            $result = substr($this->buffer, 0, $length);
            // remove $length characters from the buffer
            $this->buffer = substr_replace($this->buffer, '', 0, $length);
        }

        return $result;
    }

    /**
     * Fake write method, do nothing except return the pseudo "writen" length, i.e. the length of data
     *
     * {@inheritDoc}
     */
    public function write(string $data, $length=null) : int
    {
        if (is_null($this->buffer)) {
            throw new StreamException(sprintf("Cannot write to closed (null) stream"));
        }

        if (is_null($length)) {
            $length = strlen($data);
        }

        if ($length<0) {
            throw new \InvalidArgumentException("Cannot write a negative count of bytes");
        }

        return min($length, strlen($data));
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        $this->buffer = null;
    }
}
