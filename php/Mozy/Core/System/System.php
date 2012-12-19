<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class System extends Object implements Singleton {

    const TEMPDIR = '/tmp/';
    protected $os;
    protected $version;
    protected $release;
    protected $architecture;
    protected $hostname;
    protected $process;
    protected $loginName;

    protected function __construct() {
        $i = posix_uname();
        $this->os           = PHP_OS; // replaces $i['sysname'];
        $this->version      = $i['version'];
        $this->release      = $i['release'];
        $this->architecture = $i['machine'];
        $this->hostname     = $i['nodename'];
        $this->process      = CurrentProcess::construct($this);
        $this->loginName    = posix_getlogin();
    }

    public function getUserByID( $id ) {
        return User::getByID( $id );
    }

    public function getUserByName ( $name ) {
        return User::getByName( $name );
    }

    public function getGroupByID( $id ) {
        return Group::getByID( $id );
    }

    public function getGroupByName ( $name ) {
        return Group::getByName( $name );
    }

    public function createIO( $blocking = false, $mode = 0600 ) {
        return $this->createPipe( $blocking, $mode );
    }

    public function createPipe( $blocking = false, $mode = 0600 ) {
        return Pipe::construct( 'pipe', $blocking, $mode );
    }

    public function createSharedMemory( $blocking = false, $mode = 0600 ) {
        return SharedMemory::construct( 'm', 1000, $blocking, $mode );
    }
}
?>