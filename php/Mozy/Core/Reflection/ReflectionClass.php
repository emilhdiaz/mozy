<?php
namespace Mozy\Core\Reflection;

class ReflectionClass extends \ReflectionClass implements Documented {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $namespace;

    public function __construct($name) {
        parent::__construct($name);
        $this->namespace = get_namespace($name);
    }

    public function getNamespace() {
        return ReflectionNamespace::construct($this->namespace);
    }

    public function getParentClass() {
        return ReflectionClass::construct(get_parent_class($this->name));
    }

    public function getParentClasses() {
        $parents = [];
        foreach(class_parents($this->name, false) as $name) {
            $parents[$name] = ReflectionClass::construct($name);
        }
        return $parents;
    }

    public function getTraits() {
        $traits = [];
        foreach(class_uses($this->name, false) as $name) {
            $traits[$name] = ReflectionClass::construct($name);
        }
        return $traits;
    }

    public function getInterfaces() {
        $interfaces = [];
        foreach(class_implements($this->name, false) as $name) {
            $traits[$name] = ReflectionClass::construct($name);
        }
        return $interfaces;
    }

    public function getConstructor() {
        return $this->method('__construct');
    }

    public function getMethod($name) {
        return ReflectionMethod::construct($this->name, $name);
    }

    public function getMethods($filter = -1) {
        $methods = [];
        foreach(parent::getMethods($filter) as $method) {
            $methods[$method->name] = $this->method($method->name);
        }
        return $methods;
    }

    public function getProperty($name) {
        return ReflectionProperty::construct($this->name, $name);
    }

    public function getProperties($filter = -1) {
        $properties = [];
        foreach(parent::getProperties($filter) as $property) {
            $properties[$property->name] = $this->property($property->name);
        }
        return $properties;
    }

    public function getComment() {
        return ReflectionComment::construct($this);
    }

    public function isSingleton() {
        return $this->implementsInterface(\Mozy\Core\Singleton);
    }

    public function isImmutable() {
        return $this->implementsInterface(\Mozy\Core\Immutable);
    }

    public function isAncestorOf( $name ) {
        return is_a($name, $this->name);
    }

    public function validate() {
        /* Singleton Implementation Check */
        if( $this->isSingleton() && $this->constructor->isPublic() ) {
            throw new SingletonImplementationException($this->name);
        }
    }

    public function extend($extension) {
        if( class_exists($extension) )
            throw new \Exception('Cannot redeclare class $extension as extention of ' . $this->name);

        create_new_class($extension, $this->name);
        return $extension;
    }

    public static function exists( $name ) {
        return class_exists( $name );
    }

}
?>