<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class StdErr extends Object implements IO, Singleton {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    protected function __construct( $blocking = false ) {
        $this->path     = 'php://stderr';
        $this->mode     = 0600;
        $this->blocking = (bool) $blocking;
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
        $data = trim($data);
        $bytes = fwrite($this->resource, $data . "\n");
    }

    public function readLine() {
        throw new \Exception("Cannot read from StdErr");
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
        throw new \Exception("Cannot remove StdErr");
    }
}
?>