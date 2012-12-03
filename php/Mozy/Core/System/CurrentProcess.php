<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class CurrentProcess extends Process implements Singleton {

    protected $system;

    /**
     * @restricted System
     */
    private static function construct( System $system ) {
        return parent::_construct_($system);
    }

    public function __construct( System $system ) {
        $this->id = posix_getpid();
    }

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

    public function close() {
        exit(0);
    }
    
    public function fork() {
        
    }

    public function kill() {
        posix_kill( $this->id, SIGKILL );
    }

    public function changePriority( $priority ) {
        proc_nice($priority);
    }
}
?>