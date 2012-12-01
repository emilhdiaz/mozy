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
            $frame = get_calling_frame();

            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedMethodAccessException($name, null, $frame->caller, $frame->line);

            $method->setAccessible( true );
            return $method->invokeArgs($this, $arguments);
        }

        /* Indirect Getter Access */
        $getter = 'get' . ucfirst($name);
        if( method_exists($this, $getter) ) {
           $method = ReflectionMethod::construct($this, $getter);
            $frame = get_calling_frame();

            // check accessibility
            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedPropertyAccessException($name, null, $frame->caller, $frame->line);

            $method->setAccessible( true );
            return $method->invokeArgs($this, $arguments);
        }

        $frame = get_calling_frame();
        throw new UndefinedMethodException($name, null, $frame->caller, $frame->line);
    }
}
?>