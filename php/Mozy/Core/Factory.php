<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;

abstract class Factory extends Object {

    protected static $objects    = [];

    private static function construct() {}

    public static function instance( ReflectionClass $class, array $args ) {
        /* Validate Class Definition */
        if ( !array_key_exists( $class->name, self::$objects ) )
            $class->validate();

        /* Singleton Creation */
        if ( $class->isSingleton() )
            return self::singletonConstruction( $class, $args );

        if ( $class->isImmutable() )
            return self::immutableConstruction( $class, $args );

        /* Normal Creation */
        else
            return self::normalConstruction( $class, $args );
    }

    private static function normalConstruction( ReflectionClass $class, array $args ) {
        /* Initialize Array */
        if ( !array_key_exists($class->name, self::$objects) )
            self::$objects[$class->name] = [];

        /* Instantiate Object */
        $object = self::instantiate($class, $args);
        array_push(self::$objects[$class->name], $object);

        return $object;
    }

    private static function singletonConstruction( ReflectionClass $class, array $args ) {
        #TODO: What should happen to the args on a second invocation? invoke constructor again? or throw warning?

        /* Singleton already exists */
        if ( array_key_exists($class->name, self::$objects) )
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
        if ( !array_key_exists($class->name, self::$objects) )
            self::$objects[$class->name] = [];

        /* Immutable already exists */
        if ( array_key_exists($identifier, self::$objects[$class->name]) )
            return self::$objects[$class->name][$identifier];

        /* Instantiate Object */
        $object = self::instantiate($class, $args);
        self::$objects[$class->name][$identifier] = $object;

        return $object;
    }

    private static function instantiate( ReflectionClass $class, array $args ) {
        static $uid = 1;

        $constructor = $class->constructor;
        $baseConstructor = ReflectionMethod::construct('Mozy\Core\Object', '__construct');

        $object = $class->newInstanceWithoutConstructor();

        # The magic #

        // always call the Object base constructor
        $baseConstructor->setAccessible( true );
        $baseConstructor->invoke($object);

        // assign the class reflector
        $classProperty = $class->property('class');
        $classProperty->setAccessible( true );
        $classProperty->setValue( $object, $class );

        // assign the uid
        $classProperty = $class->property('uid');
        $classProperty->setAccessible( true );
        $classProperty->setValue( $object, $uid++ );

        // if class has it's own constructor defined then call it too
        if ( $constructor != $baseConstructor  ) {
            $constructor->setAccessible( true );
            $constructor->invokeArgs( $object, $args );
        }

        return $object;
    }

    private static function assignClass( ReflectionClass $class, Object $object ) {
        $classProperty = $class->property('class');
        $classProperty->setAccessible( true );
        $classProperty->setValue( $object, $class );
    }
}
?>