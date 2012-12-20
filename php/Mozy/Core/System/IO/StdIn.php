<?php
namespace Mozy\Core\System\IO;

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
        $this->blocking = (bool) $blocking;
        $this->open();
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
    	stream_set_blocking($this->resource, $this->blocking);
    	$data = trim(stream_get_contents($this->resource));
        return $data;
    }

   	public function readChar() {
   		stream_set_blocking($this->resource, $this->blocking);
   		$data = fgetc($this->resource);
   		return $data;
   	}

    public function readLine() {
    	stream_set_blocking($this->resource, $this->blocking);
        $data = trim(fgets($this->resource));
        return $data;
    }

    public function setBlocking( $blocking ) {
        $this->blocking = (bool) $blocking;
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