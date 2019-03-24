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
