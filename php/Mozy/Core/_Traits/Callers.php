<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;

trait Callers {
    /**
     * Magic Static Caller
     */
    public function __call( $name, $arguments ) {
        /* Delegate to Declared Method */
        if( method_exists($this, $name) ) {
            $method = ReflectionMethod::construct($this, $name);
            $caller = get_calling_class();

            if( !$method->isAllowedFor($caller) )
                throw new UnauthorizedMethodAccessException($name);

            $method->setAccessible( true );
            return $method->invokeArgs($this, $arguments);
        }

        /* Indirect Getter Access */
        $getter = 'get' . ucfirst($name);
        if( method_exists($this, $getter) ) {
           $method = ReflectionMethod::construct($this, $getter);
            $caller = get_calling_class();

            // check accessibility
            if( !$method->isAllowedFor($caller) )
                throw new UnauthorizedPropertyAccessException($name);

            $method->setAccessible( true );
            return $method->invokeArgs($this, $arguments);
        }

        throw new UndefinedMethodException($name);
    }
}
?>