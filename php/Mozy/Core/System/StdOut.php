<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class StdOut extends Object implements IO, Singleton {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    protected function __construct( $blocking = false ) {
        $this->path     = 'php://stdout';
        $this->mode     = 0600;
        $this->blocking = (bool) $blocking;
        $this->open();
    }

    public function setPath( $path ) {
        if( !file_exists($path) ) {
            throw new \Exception('File description path is invalid.');
        }

        $this->path = $path;
        $this->resource = null;
        $this->open();
    }

    public function open() {
        if( $this->isOpen() )
            return;

        $this->resource   = fopen($this->path, 'r+');
        stream_set_blocking($this->resource, $this->blocking);
        return $this;
    }

    public function isOpen() {
        return (bool) is_resource($this->resource);
    }

    public function write( $data ) {
        $data = convert($data);
        $bytes = fwrite($this->resource, $data);

        debug("Wrote ($bytes bytes): $data");
    }

    public function writeLine( $data ) {
        $this->write($data);
        fwrite($this->resource, PHP_EOL);
    }

    public function readLine() {
        throw new \Exception("Cannot read from StdOut");
    }

    public function setBlocking( $blocking ) {
        $this->blocking = (bool) $blocking;
        stream_set_blocking($this->resource, $this->blocking);
    }

    public function close() {
        if( $this->isOpen() ) {
            fclose($this->resource);
        }
        return $this;
    }

    public function remove() {
        throw new \Exception("Cannot remove StdOut");
    }
}
?>