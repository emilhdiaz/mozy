<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

class System extends Object {

    public function getGroupByGID( $gid ) {
        return Group::getByGID( $gid );
    }

    public function getGroupByName ( $name ) {
        return Group::getByName( $name );
    }
}
?>