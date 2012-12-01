<?php
namespace Mozy\Core;

abstract class API extends Object implements Singleton {

    protected $name;

    protected function __construct() {
        $name = $this->class->shortName;
        $name = str_replace('API', '', $name);
        $this->name = $name;
    }

}
?>
