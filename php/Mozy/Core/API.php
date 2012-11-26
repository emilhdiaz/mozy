<?php
namespace Mozy\Core;

abstract class API extends Object implements Singleton {
    
    protected $name;
    
    protected function __construct() {
        $name = $this->class->getShortName();
        $name = str_replace('API', '', $name);
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }

}
?>
