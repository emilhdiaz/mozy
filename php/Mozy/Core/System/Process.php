<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class Process extends Object {

    protected $id;
    protected $proc;
    protected $in;
    protected $out;
    protected $command;

    /**
     * @restricted System
     */
    private static function construct( Command $command, Pipe $in, Pipe $out, Pipe $err = null ) {
        return parent::_construct_($command, $in, $out, $err);
    }

    public function __construct( Command $command, Pipe $in, Pipe $out, Pipe $err = null ) {
        $this->proc = proc_open( (string) $command, [$in->stream, $out->stream, STDERR], $pipes);

        if( !is_resource($this->proc) )
            throw new \Exception("Command could not be executed as a new process.");

        $this->id       = proc_get_status($this->proc)['pid'];
        $this->in       = $in;
        $this->out      = $out;
        $this->command  = $command;
    }

    /**
     * Checks if the process is still running
     */
    public function isRunning() {
        return (is_resource($this->proc) ? proc_get_status($this->proc)['running'] : false);
    }

    /**
     * Get the process group ID
     */
    public function getGroupLeaderID() {
        return posix_getpgid( $this->id );
    }

    /**
     * Set the process group ID
     */
    public function setGroupLeaderID( $pgid ) {
        posix_setpgid( $this->id, $pgid );
    }

    /**
     * Get the process session ID
     */
    public function getSessionLeaderID() {
        return posix_getsid( $this->id );
    }

    /**
     * Closed the process
     */
    public function close() {
        if( $this->isRunning() )
            proc_close( $this->proc );
    }

    /**
     * Kills the process
     */
    public function kill() {
        if( $this->isRunning() )
            proc_terminate( $this->proc );
    }
}
?>