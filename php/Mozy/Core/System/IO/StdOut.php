<?php
namespace Mozy\Core\System\IO;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class StdOut extends Object implements IO, Singleton {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    protected function __construct( $blocking = false ) {
        $this->path     = 'php://output';
        $this->mode     = 0600;
        $this->blocking = (bool) $blocking;
        $this->open();
    }

    public function open() {
        if ( $this->isOpen() )
            return;

        $this->resource   = fopen($this->path, 'r+');
        stream_set_blocking($this->resource, $this->blocking);
        return $this;
    }

    public function isOpen() {
        return (bool) is_resource($this->resource);
    }

    public function write( $data ) {
    	$data = _S($data);
		$bytes = fwrite($this->resource, $data);
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
        if ( $this->isOpen() ) {
            fclose($this->resource);
        }
        return $this;
    }

    public function remove() {
        throw new \Exception("Cannot remove StdOut");
    }

    public function buffer() {
    	ob_start();
    	return $this;
    }

    public function getContents() {
    	return ob_get_contents();
    }

    public function clean() {
    	ob_clean();
    	return $this;
    }

    public function flush() {
    	ob_flush();
    	return $this;
    }

    public function autoFlush( $flag = true ) {
    	ob_implicit_flush((bool) $flag);
    	return $this;
    }

    public function end() {
    	ob_end_clean();
    	return $this;
    }
}
?>