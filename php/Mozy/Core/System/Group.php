<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Immutable;

final class Group extends Object implements Immutable {

    protected $gid;
    protected $name;
    protected $password;
    protected $members;

    /**
     * @restricted System
     */
    private static function construct( $gid, $name, $password, $members ) {
        return parent::_construct_( $gid, $name, $password, $members );
    }

    protected function __construct( $gid, $name, $password, $members ) {
        $this->gid      = $gid;
        $this->name     = $name;
        $this->password = $password;
        $this->members  = $members;
    }

    public static function getByGID( $gid ) {
        $i = posix_getgrgid( $gid );
        return static::construct($i['gid'], $i['name'], $i['passwd'], $i['members']);
    }

    public static function getByName( $name ) {
        $i = posix_getgrnam( $name );
        return static::construct($i['gid'], $i['name'], $i['passwd'], $i['members']);
    }
}
?>