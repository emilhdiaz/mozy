<?php
namespace Mozy\Core;

abstract class API extends Object implements Singleton {

    public function getName() {
    	return str_replace('API', '', $this->class->shortName);
    }
}
?>
