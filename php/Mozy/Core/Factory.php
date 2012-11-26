<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;

final class Factory extends Object implements Singleton {

    protected static $self;
    protected $objects    = [];
    protected $clones     = [];
    protected $singletons = [];
    protected $reflectors = [];
    protected $extensions = [];

    public static function construct() {
        if( !self::$self ) {
            $class = new ReflectionClass(Factory);
            $object = $class->newInstanceWithoutConstructor() ;

            $baseConstructor = $class->getConstructor();
            $baseConstructor->setAccessible( true );
            $baseConstructor->invokeArgs( $object, [$class] );

            self::$self = $object;
        }

        return self::$self;
    }

    public function getInstance( ReflectionClass $class, array $args ) {
        $baseConstructor = $this->reflect('Mozy\Core\Object')->getConstructor();
        $constructor = $class->getConstructor();

        $object = $class->newInstanceWithoutConstructor() ;

        // The magic.
        // always call the Object base constructor
        $baseConstructor->setAccessible( true );
        $baseConstructor->invokeArgs( $object, [$class] );

        // if class has it's own constructor defined then call it
        if( $constructor != $baseConstructor  ) {
            $constructor->setAccessible( true );
            $constructor->invokeArgs( $object, $args );
        }

        if( !array_key_exists($class->getName(), $this->objects) ) {
            // check class definitions
            $class->validate();
            $this->objects[$class->getName()] = [];
        }

        array_push($this->objects[$class->getName()], $object);

        return $object;
    }

    public function getSingleton( ReflectionClass $class, array $args ) {
        if( !array_key_exists($class->getName(), $this->singletons) ) {
            $this->singletons[$class->getName()] = $this->getInstance($class, $args);
        }

        #TODO: What should happen to the args on a second invocation?
        # invoke constructor again? or throw warning?

        return $this->singletons[$class->getName()];
    }

    public function extend( ReflectionClass $class, $extension ) {
        if( !array_key_exists($extension, $this->extensions) ) {
            create_new_class($extension, $class->name);
            $this->extensions[$extension] = $class->getName();
        }

        return $extension;
    }

    public function reflect( $className ) {
        if( !array_key_exists($className, $this->reflectors) ) {
            $this->reflectors[$className] = new ReflectionClass($className);
        }

        return $this->reflectors[$className];
    }
}
?>