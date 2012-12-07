<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Immutable;

final class User extends Object implements Immutable {

    protected $id;
    protected $name;
    protected $gid;
    protected $home;
    protected $shell;
    protected $password;
    protected $hasRootPrivileges;

    /**
     * @allow System
     */
    private static function construct( $id, $name, $gid, $home, $shell, $password ) {
        return parent::_construct_( $id, $name, $gid, $home, $shell, $password );
    }

    protected function __construct( $id, $name, $gid, $home, $shell, $password ) {
        $this->id       = $id;
        $this->name     = $name;
        $this->gid      = $gid;
        $this->home     = $home;
        $this->shell    = $shell;
        $this->password = $password;
    }

    public static function getByID( $id ) {
        $i = posix_getpwuid( $id );
        return static::construct( $i['uid'], $i['name'], $i['gid'], $i['dir'], $i['shell'], $i['passwd'] );
    }

    public static function getByName( $name ) {
        $i = posix_getpwnam( $name );
        return static::construct( $i['uid'], $i['name'], $i['gid'], $i['dir'], $i['shell'], $i['passwd'] );
    }
}
?>