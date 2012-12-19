<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class StdIn extends Object implements IO, Singleton {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    protected function __construct( $blocking = true ) {
        $this->path     = 'php://stdin';
        $this->mode     = 0600;
        $this->open();
        $this->setBlocking($blocking);
    }

    public function open() {
        if ( $this->isOpen() )
            return;

        $this->resource = fopen($this->path, 'r');
        return $this;
    }

    public function isOpen() {
        return (bool) is_resource($this->resource);
    }

    public function write( $data ) {
        throw new \Exception("Cannot write to StdIn");
    }

    public function read() {
    	$data = trim(stream_get_contents($this->resource));

    	debug("Read (" . strlen($data) . " bytes): $data");
        return $data;
    }

   	public function readChar() {
   		$data = fgetc($this->resource);
   		return $data;
   	}

    public function readLine() {
        $data = trim(fgets($this->resource));

		debug("Read (" . strlen($data) . " bytes): $data");
        return $data;
    }

    public function setBlocking( $blocking ) {
        $this->blocking = (bool) $blocking;
        stream_set_blocking($this->resource, $this->blocking);
    }

    public function close() {
        if ( $this->isOpen() ) {
            fclose($this->resource);
        }
        return $this;
    }

    public function remove() {
        throw new \Exception("Cannot remove StdIn");
    }
}
?>