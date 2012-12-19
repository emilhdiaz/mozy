<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class Pipe extends Object implements IO {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    /**
     * @allow System
     */
    private static function construct( $key, $blocking = false, $mode = 0600 ) {
        return parent::_construct_( $key, $blocking, $mode );
    }

    protected function __construct( $key, $blocking = false, $mode = 0600 ) {
        $this->path     = System::TEMPDIR . $key . rand();
        $this->mode     = (int) $mode;
        $this->blocking = (bool) $blocking;
        $this->open();
    }

    public function open() {
        if ( $this->isOpen() )
            return;

        if ( file_exists($this->path) ) {
            throw new \Exception('Pipe already exists');
        }

        if ( !posix_mkfifo($this->path, $this->mode) ) {
            throw new \Exception('Unable to create Pipe');
        }

        $this->resource   = fopen($this->path, 'r+');
        stream_set_blocking($this->resource, $this->blocking);
        return $this;
    }

    public function isOpen() {
        return (bool) (file_exists($this->path) && is_resource($this->resource));
    }

    public function write( $data ) {
        $data = _S($data);
        $bytes = fwrite($this->resource, $data);

        debug("Wrote ($bytes bytes): $data");
    }

    public function writeLine( $data ) {
        $this->write($data);
        fwrite($this->resource, PHP_EOL);
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
        debug("Destroying pipe file " . $this->path);
        $this->close();
        unlink($this->path);
        unset($this);
    }

    public function __destruct() {
        $this->close();
        #TODO: add check to see if parent or child process and destroy pipe too
    }
}
?>