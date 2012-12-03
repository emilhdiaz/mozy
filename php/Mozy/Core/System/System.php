<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class System extends Object implements Singleton {

    const TEMP = '/tmp/';
    protected $os;
    protected $version;
    protected $release;
    protected $architecture;
    protected $hostname;
    protected $console;
    protected $loginName;
    protected $childProcesses = [];

    protected function __construct() {
        $i = posix_uname();
        $this->os           = PHP_OS; // replaces $i['sysname'];
        $this->version      = $i['version'];
        $this->release      = $i['release'];
        $this->architecture = $i['machine'];
        $this->hostname     = $i['nodename'];
        $this->console      = Console::construct();
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

    public function createPipe( $blocking = false, $mode = 0600 ) {
        return Pipe::construct( self::TEMP . 'pipe-' . rand(), $blocking, $mode );
    }

    /**
     * Executes a blocking command in non-interactive mode
     */
    public function execute( Command $command ) {
        exec( (string) $command, $output );
        return implode(' ', $output);
    }

    /**
     * Executes a blocking command in interactive mode
     */
    public function executeInteractive( Command $command ) {
        system( (string) $command );
    }

    /**
     * Executes an asynchronous command
     */
    public function executeAsynchronous( Command $command ) {
        // create non-blocking pipes
        $in  = $this->createPipe();
        $out = $this->createPipe();

        $process = Process::construct($command, $in, $out);
        $this->childProcesses[$process->id] = $process;

        return $process;
    }

    public function waitForChildProcesses() {
        // must ensure child process close
        foreach( $this->childProcesses as $childProcess ) {
            $childProcess->close();
        }
    }

    public function killChildProcesses() {
        // signal SIGTERM but do not wait
        foreach( $this->childProcesses as $childProcess ) {
            $childProcess->kill();
        }
    }

}
?>