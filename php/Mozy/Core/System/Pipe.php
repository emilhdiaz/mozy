<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class Pipe extends Object {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $stream;

    /**
     * @restricted System
     */
    private static function construct( $path, $blocking = false, $mode = 0600 ) {
        return parent::_construct_( $path, $blocking, $mode );
    }

    protected function __construct( $path, $blocking = false, $mode = 0600 ) {
        if( file_exists($path) ) {
            throw new \Exception('Pipe already exists');
        }

        if( !posix_mkfifo($path, $mode) ) {
            throw new \Exception('could not create Pipe');
        }

        $this->path     = (string) $path;
        $this->mode     = (int) $mode;
        $this->blocking = (bool) $blocking;
        $this->stream   = fopen($path, 'w+');

        stream_set_blocking($this->stream, $blocking);
    }

    public function write( $data ) {
        $bytes = fwrite($this->stream, $data);

        echo "Wrote ($bytes bytes): $data \n";
    }

    public function readLine() {
        $data = fgets($this->stream);
        echo "Read (" . strlen($data) . " bytes): $data";
        return $data;
    }

    public function setBlocking( $blocking ) {
        $this->blocking = (bool) $blocking;
        stream_set_blocking($this->stream, $this->blocking);
    }

    public function close() {
        fclose($this->stream);
        $this->stream = null;
    }

    public function __destruct() {
        if( $this->stream )
            $this->close();

        unlink($this->path);
    }
}
?>