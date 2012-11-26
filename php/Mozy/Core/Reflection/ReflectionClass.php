<?php
namespace Mozy\Core\Reflection;

use Mozy\Core;
use Mozy\Core\ApplicationContext;

class ReflectionClass extends \ReflectionClass implements Documented {

    public $namespace;

    public function __construct($name) {
        parent::__construct($name);
        $this->namespace = Core\get_namespace($name);
    }

    public function getNamespace() {
        return new ReflectionNamespace($this->namespace);
    }

    public function getParentClass() {
        return new ReflectionClass(get_parent_class($this->name));
    }

    public function getParentClasses() {
        $parents = [];
        foreach(class_parents($this->name, false) as $name) {
            $parents[$name] = new ReflectionClass($name);
        }
        return $parents;
    }

    public function getTraits() {
        $traits = [];
        foreach(class_uses($this->name, false) as $name) {
            $traits[$name] = new ReflectionClass($name);
        }
        return $traits;
    }

    public function getInterfaces() {
        $interfaces = [];
        foreach(class_implements($this->name, false) as $name) {
            $traits[$name] = new ReflectionClass($name);
        }
        return $interfaces;
    }

    public function getConstructor() {
        return $this->getMethod('__construct');
    }

    public function getMethod($name) {
        return new ReflectionMethod($this->name, $name);
    }

    public function getMethods($filter = -1) {
        $methods = [];
        foreach(parent::getMethods($filter) as $method) {
            $methods[$method->name] = $this->getMethod($method->name);
        }
        return $methods;
    }

    public function getProperty($name) {
        return new ReflectionProperty($this->name, $name);
    }

    public function getProperties($filter = 0) {
        $properties = [];
        foreach(parent::getProperties($filter) as $property) {
            $properties[$property->name] = $this->getProperty($property->name);
        }
        return $properties;
    }

    public function extend($extension) {
        global $framework;
        return $framework->factory->extend($this, $extension);
    }

    public function getComment() {
        return new ReflectionComment($this);
    }

    public static function exists($className) {
        return class_exists($className);
    }

    public function validate() {
        /* Singleton Implementation Check */
        if( $this->implementsInterface(Core\Singleton) && $this->getConstructor()->isPublic() ) {
            throw new SingletonImplementationException($this->name);
        }
    }
}
?>