[![Latest Stable Version](https://poser.pugx.org/mracine/php-streams/v/stable)](https://packagist.org/packages/mracine/php-streams)
[![PHP 7 ready](https://php7ready.timesplinter.ch/matracine/php-streams/master/badge.svg)](https://travis-ci.org/matracine/php-streams)
[![License](https://poser.pugx.org/mracine/php-streams/license)](https://packagist.org/packages/mracine/php-streams)
[![Travis Build Status](https://travis-ci.org/matracine/php-streams.svg?branch=master)](https://travis-ci.org/matracine/php-streams)
[![Scrutinizer Build Status](https://scrutinizer-ci.com/g/matracine/php-streams/badges/build.png?b=master)](https://scrutinizer-ci.com/g/matracine/php-streams/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matracine/php-streams/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/matracine/php-streams/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/matracine/php-streams/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/matracine/php-streams/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/matracine/php-streams/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Maintainability](https://api.codeclimate.com/v1/badges/e6d172b10c3f12e1bb35/maintainability)](https://codeclimate.com/github/matracine/php-streams/maintainability)

# PHP STREAMS ABSTRACTION LIBRARY

**mracine\Streams** is a library that provide streams abstraction to use differents kind of resources (files, sockets, pipes, process input/output, strings) as streams provider. It makes testing simple for classes which use the mracine\Sreams\Stream interface.

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

    composer require mracine/streams

## Usage

The mracine\Streams\Stream interface provides :

 - read method : get bytes from a stream
 - write method : push bytes to a stream
 - close method : release the stream

There are actualy two classes implementing the Stream interface :

- mracine\Streams\ResourceStream : abstract a PHP resource (fils, socket, UNIX pipes, process....)
- mracine\Streams\StringStream : abstract a PHP string

```php
<?php
use mracine\Streams;

$socketStream = new Streams\ResourceStream(stream_socket_client('tcp://'.$serverIP.':'.$serverPort));
$stringStream = new Streams\StringStream('Hello World !');

// talk function does not have to know what kind of "resource" it communicate with
// Could be a file, a socket, a process or even a string 
function talk(Streams\Stream $stream)
{
    $stream->write('Hi !');
    $bytes = $stream->read(5);

    return $bytes;
}

echo talk($socketStream): // Will echo 5 firsts bytes the server returned
echo talk($stringStream); // Will echo "Hello"
echo talk($stringStream); // Will echo " Worl"
echo talk($stringStream); // Will echo "d !"


```
