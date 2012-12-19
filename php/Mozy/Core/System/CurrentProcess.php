<?php
namespace Mozy\Core\System;

use Mozy\Core\Singleton;
use Mozy\Core\System\IO\StdIn;
use Mozy\Core\System\IO\StdOut;
use Mozy\Core\System\IO\StdErr;

class CurrentProcess extends Process implements Singleton {

    protected $system;
    protected $lock;
    protected $children = [];
    protected static $maxChildren = 30;

    /**
     * @allow System
     */
    private static function construct( System $system ) {
        return parent::_construct_($system);
    }

    /**
     * @allow System
     */
    protected function __construct( System $system ) {
        $this->system = $system;
        $this->id  = posix_getpid();
        $this->in  = StdIn::construct();
        $this->out = StdOut::construct();
        $this->err = StdErr::construct();
        declare(ticks = 1);
        pcntl_signal(SIGTERM, [$this, 'terminate'], false);
        pcntl_signal(SIGINT,  [$this, 'terminate'], false);
        pcntl_signal(SIGCHLD, [$this, 'processResponses'], false);
    }

    /**
     * Always returns TRUE for current process
     */
    public function isRunning() {
        return true;
    }

    /**
     * Get the parent process ID
     */
    public function getParentID() {
        return posix_getppid();
    }

    /**
     * Makes the current process a session leader
     */
    public function makeSessionLeader() {
        posix_setsid();
    }

    /**
     * Get the real User of the process
     * @returns User
     */
    public function getUser() {
        return $this->system->userByID(posix_getuid());
    }

    /**
     * Set the real User of the process (requires privileged root access)
     */
    public function setUser( User $user ) {
        posix_setuid( $user->id );
    }

    /**
     * Get the real Group of the process
     * @returns Group
     */
    public function getGroup() {
        return $this->system->groupByID(posix_getgid());
    }

    /**
     * Set the real Group of the process (requires privileged root access)
     */
    public function setGroup( Group $group ) {
        posix_setgid( $group->id );
    }

    /**
     * Get the effective User of the process
     * @returns User
     */
    public function getEffectiveUser() {
        return $this->system->userByID(posix_geteuid());
    }

    /**
     * Set the effective User of the process
     */
    public function setEffectiveUser( User $user ) {
        posix_seteuid( $user->ID );
    }

    /**
     * Get the effective Group of the process
     * @returns Group
     */
    public function getEffectiveGroup() {
       return $this->system->groupByID(posix_getegid());
    }

    /**
     * Set the effective Group of the process
     */
    public function setEffectiveGroup( Group $group ) {
        posix_setegid( $group->ID );
    }

    /**
     * Get the current working directory (absolute path) of the process.
     */
    public function getCWD() {
        return posix_getcwd();
    }

    /**
     * Get the Groups the process belongs to
     * @returns Array<Group>
     */
    public function getGroups() {
        $groups = [];
        foreach( posix_getgroups() as $GID) {
            $groups[$GID] = $this->system->groupByGID($GID);
        }
        return $groups;
    }

    public function alarm( $seconds ) {
        pcntl_alarm( $seconds );
    }

    /**
     * End process execution normally.
     * Do not wait for children and do not process their responses.
     * Close any communication streams to the children.
     */
    public function close() {
        $this->abandonChildren();

        exit(0);
    }

    /**
     * End process execution abnormally.
     * Wait for children to terminate as well but do not process their resposes.
     * Close any communication streams to the children.
     */
    public function terminate() {
        $this->terminateChildren();

        throw new \Exception("Process PID(". $this->id .") terminated\n");
    }

    /**
     * End process execution abnormally.
     * Kill children as well but do not wait for them and do not process their resposes.
     * Close any communication streams to the children.
     */
    public function kill() {
        $this->killChildren();

        throw new \Exception("Process PID(". $this->id .") killed");
    }

    /**
     * Wait for all children to exit by polling for new SIGCHLD signals.
     * SIGCHLD handler will close communication streams.
     */
    public function waitForChildren() {
        while( count($this->children) > 0 ) {
            pcntl_signal_dispatch();
            usleep(1000);
        }
    }

    /**
     * Close communication streams to all live children.
     * Remove from children list to prevent SIGCHLD handler from processing responses.
     */
    public function abandonChildren() {
        // Clear children list as quickly as possible
        $children = $this->children;
        $this->children = [];

        foreach( $children as $child ) {
            $child->closeStreams();
        }
    }

    /**
     * Terminate all children (terminate is a blocking call).
     * Remove from children list to prevent SIGCHLD handler from processing responses.
     */
    public function terminateChildren() {
        // Clear children list as quickly as possible
        $children = $this->children;
        $this->children = [];

        foreach( $children as $child ) {
            $child->terminate();
        }
    }

    /**
     * Kill all children (kill is a non blocking call).
     * Remove from children list to prevent SIGCHLD handler from processing responses.
     */
    public function killChildren() {
        // Clear children list as quickly as possible
        $children = $this->children;
        $this->children = [];

        foreach( $children as $child ) {
            $child->kill();
        }
    }

    public function processResponses() {
        $pid = pcntl_wait( $status, WNOHANG);
        while( $pid > 0 ) {
            if ( !array_key_exists($pid, $this->children) ) {
                debug("Child PID($pid) was already removed from child list. \n");
            }
            else {
                debug("Child PID($pid) was processed and removed from child list. \n");
                $this->children[$pid]->processResponse();
                unset($this->children[$pid]);
            }

            /*check for more dead children in this signal */
            $pid = pcntl_wait( $status, WNOHANG);
        }
    }

    /**
     * Executes an asynchronous command
     */
    public function executeAsynchronous( Command $command, \Closure $localCallback = null ) {
        return $this->fork( $command, $localCallback );
    }

    public function daemonize( $name ) {
        $log = 'daemon.log';

        /* Check if already a daemon */
        if ( $this->parentID == 1 )
            throw new \Exception('Currently running as daemon, cannot daemonize again');

        /* Fork the parent process */
        $pid = pcntl_fork();

        if ( $pid < 0 ) {
            throw new \Exception("Could not daemonize process");
        }

        /* Adjust in child */
        if ( $pid == 0 ) {

            /* Assign the new PID */
            $this->id = posix_getpid();

            /* Check if process is already a daemon */
            $this->lock = fopen('daemon.lock', 'c+');
            if ( !flock($this->lock, LOCK_EX | LOCK_NB) )
                throw new \Exception('Daemon is already running');

            /* Write PID lock file for daemon */
            fseek($this->lock, 0);
            ftruncate($this->lock, 0);
            fwrite($this->lock, $this->id);
            fflush($this->lock);

            /* Change the process title */
            $this->title = $name;

            /* Change the file mode mask */
            umask(0);

            /* Detach from terminal */
            $this->makeSessionLeader();

            /* Re-install signal handlers */
            pcntl_signal(SIGTERM, [$this, 'terminate'], false);
            pcntl_signal(SIGINT,  [$this, 'terminate'], false);
            pcntl_signal(SIGCHLD, [$this, 'processResponses'], false);

            /* Change the current working directory */

            /* Redirect standard files to /dev/null */
            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);

            global $STDIN, $STDOUT, $STDERR;
            $STDIN  = fopen('/dev/null', 'r');
            $STDOUT = fopen('/dev/null', 'ab');
            $STDERR = fopen('/dev/null', 'ab');

            error_log("Started $name (Daemon PID " . $this->id . ") \n", 3, $log);

            return;
        }

        /* Gracefully exit parent */
        else {
            /* Close the parent */
            $this->close();

            /* Make sure parent dies here */
            exit();
        }

    }

    public function fork( Command $childBranch, \Closure $localCallback = null, InternalCommand $parentBranch = null, $openPipes = true ) {
        $in;
        $out;
        if ( count($this->children) >= self::$maxChildren ) {
            throw new \Exception("Reached max limit (" . self::$maxChildren . ") of children");
        }

        /* Create non-blocking pipes */
        if ( $openPipes ) {
            $in  = $this->system->createIO(true);
            $out = $this->system->createIO(true);
        }

        /* Fork the parent process */
        $pid = pcntl_fork();

        if ($pid < 0) {
            throw new \Exception("Command could not be executed as a forked process");
        }

        /* Execute command in child */
        if ($pid == 0) {
            /* Need to reset children since my children are not children's children! */
            $this->children = [];

            /* Assign the new PID */
            $this->id = posix_getpid();

            /* Change the process title */
            $this->title = 'Mozy Process ' . $this->id;

            debug("New child process PID(". $this->id .") created. \n");

            #TODO: need to reinstall signal handlers to allow children to have childran

            /* Execute internal child branch */
            if ( $childBranch->class->name == 'Mozy\Core\InternalCommand' ) {
                $this->in = $in;
                $this->out = $out;
                $this->err = $out;

                $response = $childBranch();
                $this->out->write(serialize($response));

                /* Close the child */
                $this->close();
            }

            /* Execute external child branch */
            if ( $childBranch->class->name == 'Mozy\Core\ExternalCommand' ) {
                /* Redirect standard files  */
                fclose(STDIN);
                fclose(STDOUT);
                fclose(STDERR);

                if ( $openPipes ) {
                    global $STDIN, $STDOUT, $STDERR;
                    $STDIN  = fopen($in->path, 'r+');
                    $STDOUT = fopen($out->path, 'r+');
                    $STDERR = fopen('temp.err', 'w+');
                }
                pcntl_exec($childBranch->command, $childBranch->arguments);
            }

            // make sure child dies here
            exit();
        }

        /* Create child Process object in parent */
        else {
            $process = Process::construct( $pid, $childBranch, $in, $out );
            $process->callback = $localCallback;

            $this->children[$process->id] = $process;

            if ( $parentBranch ) {
                /* Execute parent branch */
                $parentBranch();

                /* Close the parent */
                $this->close();

                // make sure parent dies here
                exit();
            } else {
                /* Return execution control to caller */
                return $process;
            }
        }
    }

    public function __destruct() {
        if ( is_resource($this->lock) )
            fclose($this->lock);

        $this->waitForChildren();
    }
}
?>