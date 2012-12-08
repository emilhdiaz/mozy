<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class SharedMemory extends Object implements IO {

    protected $path;
    protected $mode;
    protected $blocking;
    protected $resource;
    protected $size;

    /**
     * @allow System
     */
    private static function construct( $key, $size = null, $blocking = false, $mode = 0600 ) {
        return parent::_construct_( $key, $size, $blocking, $mode );
    }

    protected function __construct( $key, $size = null, $blocking = false, $mode = 0600 ) {
        $this->path     = ftok(__FILE__, $key) + rand();
        $this->mode     = (int) $mode;
        $this->blocking = (bool) $blocking;
        $this->size     = (int) $size;
        $this->open();
    }

    public function open() {
        if( $this->isOpen() )
            return;

        $this->resource = shm_attach( $this->path, $this->size, $this->mode );

        if( !$this->resource ) {
            throw new \Exception("Unable to create Shared Memory segment");
        }

        shm_put_var( $this->resource, 1, null );

        return $this;
    }

    public function isOpen() {
        return (bool) is_resource($this->resource);
    }

    public function write( $data ) {
        $data = convert($data);
        shm_put_var( $this->resource, 1, $data);
        $bytes = strlen($data);

        debug("Wrote ($bytes bytes): $data");
    }

    public function readLine() {
        $data = trim(shm_get_var( $this->resource, 1));

        if( $this->blocking ) {
            while( strlen($data) <= 0 ) {
                usleep(10000);
                $data = trim(shm_get_var( $this->resource, 1));
            }
        }

        shm_put_var( $this->resource, 1, null );

        debug("Read (" . strlen($data) . " bytes): $data");
        return $data;
    }

    public function setBlocking( $blocking ) {
        $this->blocking = (bool) $blocking;
    }

    public function close() {
        if( $this->isOpen() ) {
            shm_detach( $this->resource );
        }
        return $this;
    }

    public function remove() {
        debug("Destroying shared memory " . $this->path);
        shm_remove( $this->resource );
        $this->close();
        unset($this);
    }

    public function __destruct() {
        $this->close();
        #TODO: add check to see if parent or child process and destroy pipe too
    }
}
?>