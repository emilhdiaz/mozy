<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class Process extends Object {

    public function getPath() {

    }

    /**
     * Get the current working directory (absolute path) of the process.
     */
    public function getCWD() {
        return posix_getcwd();
    }

    /**
     * Get the real group ID of the process
     */
    public function getGID() {
        posix_getgid();
    }

    /**
     * Get the effective group ID of the process.
     */
    public function getEffectiveGID() {
       return posix_getegid();
    }

    /**
     * Get the real user ID of the process
     */
    public function getUID() {
        return posix_getuid();
    }

    /**
     * Get the effective user ID of the process
     */
    public function getEffectiveUID() {
        return posix_geteuid();
    }

    public function getConsole() {

    }

}
?>