<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class Pipe extends Object implements IO {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;

    /**
     * @restricted System
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
        if( $this->isOpen() )
            return;

        if( file_exists($this->path) ) {
            throw new \Exception('Pipe already exists');
        }

        if( !posix_mkfifo($this->path, $this->mode) ) {
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
        $data = trim($data);
        $bytes = fwrite($this->resource, $data . "\n");

#        echo "Wrote ($bytes bytes): $data \n";
    }

    public function readLine() {
        $data = trim(fgets($this->resource));

#        echo "Read (" . strlen($data) . " bytes): $data \n";
        return $data;
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
#        echo "Destroying pipe file " . $this->path . "\n";
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