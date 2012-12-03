<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Immutable;

final class Group extends Object implements Immutable {

    protected $id;
    protected $name;
    protected $password;
    protected $members;

    /**
     * @restricted System
     */
    private static function construct( $id, $name, $password, $members ) {
        return parent::_construct_( $id, $name, $password, $members );
    }

    protected function __construct( $id, $name, $password, $members ) {
        $this->id       = $id;
        $this->name     = $name;
        $this->password = $password;
        $this->members  = $members;
    }

    public static function getByID( $id ) {
        $i = posix_getgrgid( $id );
        return static::construct($i['gid'], $i['name'], $i['passwd'], $i['members']);
    }

    public static function getByName( $name ) {
        $i = posix_getgrnam( $name );
        return static::construct($i['gid'], $i['name'], $i['passwd'], $i['members']);
    }
}
?>