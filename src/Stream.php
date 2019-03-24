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

/**
 * Interface Stream
 *
 * Stream abstraction
 *
 * @since 0.1
 */
interface Stream
{
    /**
     * Reads a stream
     *
     * Reads $length bytes from the stream, returns the bytes into a string
     * Must be binary safe (as fread).
     *
     * @since 0.1
     * @param int $length the numer of bytes to read
     * @return string a binary string containing the readed byes
     * @throws \InvalidArgumentException if $length is invalid (<=0)
     * @throws mracine\Streams\Exception\StreamsException when an error appends
     */
    public function read(int $length) : string;

    /**
     * Writes data to a stream
     *
     * Write $length bytes of string, if not mentioned, write all the string
     * Must be binary safe (as fread).
     * if $length is greater than string length, write all string and return number of writen bytes
     * if $length is smaller than string length, remaining bytes are losts.
     *
     * @since 0.1
     * @param string $string the data to write to the stream
     * @param int $length the numer of bytes to read
     * @return int the numer of writen bytes
     * @throws mracine\Streams\Exception\StreamsException when an error appends
     */
    public function write(string $string, $length=-1) : int;

    /**
     * Close a stream
     *
     * Release the stream (like fclose). Once closed, the stream cannot be used anymore.
     *
     * @since 0.1
     * @throws mracine\Streams\Exception\StreamsException when an error appends
     */
    public function close();
}
