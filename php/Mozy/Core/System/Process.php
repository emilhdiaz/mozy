<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Command;

class Process extends Object {

    protected $id;
    protected $in;
    protected $out;
    protected $err;
    protected $command;
    protected $callback;
    protected $status;

    /**
     * @allow CurrentProcess
     */
    private static function construct( $pid, Command $command, IO $in = null, IO $out = null, IO $err = null ) {
        return parent::_construct_( $pid, $command, $in, $out, $err );
    }

    protected function __construct( $pid, Command $command, IO $in = null, IO $out = null, IO $err = null ) {
        $this->id       = $pid;
        $this->in       = $in;
        $this->out      = $out;
        $this->err      = $err;
        $this->command  = $command;
    }

    public function setTitle( $title ) {
        setproctitle( $title );
    }

    /**
     * Checks if the process is still running
     */
    public function isRunning() {
        return (bool) (@pcntl_getpriority($this->id) !== false);
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

    public function getPriority() {
        return pcntl_getpriority( $this->id );
    }

    public function setPriority( $priority ) {
        pcntl_setpriority( $priority, $this->id );
    }

    public function setCallback( Closure $callback ) {
        $this->callback  = $callback;
    }

    public function processResponse() {
        /* Process response */
        #TODO: process response
        if( $this->callback ) {
            $response = unserialize($this->out->readLine());
            $this->callback->__invoke( $response );
        }

        $this->closeStreams();
    }


    /**
     * Destroys the communication streams to the process.
     * @allow CurrentProcess
     */
    protected function closeStreams() {
        /* Tear down communication streams */
        $this->in->remove();
        $this->out->remove();
    }

#    /**
#     * Waits for the process to exit (blocking)
#     */
#    public function wait() {
#        if( $this->isRunning() ) {
#            $pid = pcntl_waitpid( $this->id, $this->status );
#        }
#    }
#
#    /**
#     * Checks if process has exited (non-blocking)
#     */
#    public function check() {
#        if( $this->isRunning() ) {
#            pcntl_waitpid( $this->id, $this->status, WNOHANG );
#        }
#        return $this->isRunning();
#    }

    /**
     * Closes the process (blocking) and communication streams.
     */
    public function terminate() {
        if( $this->isRunning() ) {
            posix_kill( $this->id, SIGTERM );
            pcntl_waitpid( $this->id, $this->status );
        }
        $this->closeStreams();
    }

    /**
     * Kills the process (non-blocking) and communication streams.
     */
    public function kill() {
        if( $this->isRunning() ) {
            posix_kill( $this->id, SIGKILL );
            pcntl_waitpid( $this->id, $this->status, WNOHANG );
        }
        $this->closeStreams();
    }

    public function exitedNormal() {
        if( pcntl_wifexited( $this->status ) )
            return pcntl_wexitstatus( $this->status );

        return false;
    }

    public function exitedWithSignal() {
        if( pcntl_wifsignaled( $this->status ) )
            return pcntl_wtermsig( $this->status );

        return false;
    }
}
?>