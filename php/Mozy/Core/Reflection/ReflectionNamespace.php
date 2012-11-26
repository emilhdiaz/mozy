<?php
namespace Mozy\Core\Reflection;

class ReflectionNamespace implements \Reflector {

    public $name;
    
    public function __construct($namespace) {
        $this->name = $namespace;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getClasses() {
    
    }
    
    public function getConstants() {
    
    }
    
    public function getVariables() {
    
    }
    
    public function getFunctions() {
    
    }
    
    public function getFiles() {
    
    }

    public static function export() {
        
    }
    
    public function __toString() {
        return static::export();
    }
}
?>