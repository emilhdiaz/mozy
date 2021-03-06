<?php
namespace Mozy\Core\Reflect;

use Mozy\Core\Exception;

final class ReflectionNamespace implements \Reflector {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $name;

    public function __construct($namespace) {
        $this->name = $namespace;
    }

    public function getName() {
        return $this->name;
    }

    public function getClass( $class ) {
        if ( !$namespace = get_namespace( $class ) ) {
            $namespace = $this->name;
            $class = $namespace . NAMESPACE_SEPARATOR . $class;
        }

        if ( $namespace != $this->name )
            throw new \Exception("Class $class is not from this namespace");

        return ReflectionClass::construct($class);
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