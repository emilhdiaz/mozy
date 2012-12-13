<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;

final class Factory extends Object {

    protected static $objects    = [];

    private static function construct() {}

    public static function instance( ReflectionClass $class, array $args ) {
        /* Validate Class Definition */
        if( !array_key_exists( $class->name, self::$objects ) )
            $class->validate();

        /* Singleton Creation */
        if( $class->isSingleton() )
            return self::singletonConstruction( $class, $args );

        if( $class->isImmutable() )
            return self::immutableConstruction( $class, $args );

        /* Normal Creation */
        else
            return self::normalConstruction( $class, $args );
    }

    private static function normalConstruction( ReflectionClass $class, array $args ) {
        /* Initialize Array */
        if( !array_key_exists($class->name, self::$objects) )
            self::$objects[$class->name] = [];

        /* Instantiate Object */
        $object = self::instantiate($class, $args);
        array_push(self::$objects[$class->name], $object);

        return $object;
    }

    private static function singletonConstruction( ReflectionClass $class, array $args ) {
        #TODO: What should happen to the args on a second invocation? invoke constructor again? or throw warning?

        /* Singleton already exists */
        if( array_key_exists($class->name, self::$objects) )
            return self::$objects[$class->name];

        /* Instantiate Object */
        $object = self::instantiate($class, $args);
        self::$objects[$class->name] = $object;

        return $object;
    }

    private static function immutableConstruction( ReflectionClass $class, array $args ) {
        // first argument is always the identifier
        $identifier = $args[0];

        /* Initialize Array */
        if( !array_key_exists($class->name, self::$objects) )
            self::$objects[$class->name] = [];

        /* Immutable already exists */
        if( array_key_exists($identifier, self::$objects[$class->name]) )
            return self::$objects[$class->name][$identifier];

        /* Instantiate Object */
        $object = self::instantiate($class, $args);
        self::$objects[$class->name][$identifier] = $object;

        return $object;
    }

    private static function instantiate( ReflectionClass $class, array $args ) {
        $constructor = $class->constructor;
        $baseConstructor = ReflectionMethod::construct('Mozy\Core\Object', '__construct');

        $object = $class->newInstanceWithoutConstructor();

        # The magic #

        // always call the Object base constructor
        $baseConstructor->setAccessible( true );
        $baseConstructor->invoke($object);

        // assign the class reflector
        self::assignClass($class, $object);

        // if class has it's own constructor defined then call it too
        if( $constructor != $baseConstructor  ) {
            $constructor->setAccessible( true );
            $constructor->invokeArgs( $object, $args );
        }

        return $object;
    }

    public static function unserialize( $serial ) {
        $object = unserialize($serial);
        return self::revive($object);
    }

    public static function revive( $object = null ) {
        // scalar or null: just return
        if( is_scalar($object) || is_null($object) ) {
            return $object;
        }

        // array: iterate
        if( is_array($object) ) {
            foreach($object as &$value) {
                $value = self::revive($value);
            }
        }

        // object: now it gets interesting
        if( is_object($object) ) {

            // not one of ours so just return
            if( !( is_a($object, 'Mozy\Core\Object') ) ) {
                return $object;
            }

            // initialize the class reflector
            $class = self::reflect(get_class($object));
            self::assignClass( $class , $object );

            // check for singletons
            if( $class->isSingleton() ) {
                // existing singleton already registered so merge
                if( $exiting = array_value(self::$objects, $object->class->name) ) {
#                    $existing->__revive($object);
#                    return $existing;
                    return null;
                }
                // create singleton
                else {
                    self::$singletons[$object->class->name] = $object;
                }
            }

            // add to object registry
            if( !array_key_exists($object->class->name, self::$objects) ) {
                // check class definitions
                $class->validate();
                self::$objects[$object->class->name] = [];
            }

            array_push(self::$objects[$object->class->name], $object);

            return $object;
        }
    }

    private static function assignClass( ReflectionClass $class, Object $object ) {
        $classProperty = $class->property('class');
        $classProperty->setAccessible( true );
        $classProperty->setValue( $object, $class );
    }
}
?>