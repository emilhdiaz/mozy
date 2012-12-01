<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;

trait StaticCallers {
    /**
     * Magic Static Caller
     */
    public static function __callStatic( $name, $arguments ) {
        $class = get_called_class();

        /* Delegate to Declared Method */
        if( method_exists($class, $name) ) {
            $method = ReflectionMethod::construct($class, $name);
            $frame = get_calling_frame();

            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedMethodAccessException($name, null, $frame->caller, $frame->line);

            $method->setAccessible( true );
            return $method->invokeArgs(null, $arguments);
        }

        /* Virtal Object Method */

        // construction is by default public
        if( in_array( $name, ['construct', '_construct_'] ) )
            return Factory::instance(ReflectionClass::construct($class), $arguments);

        $frame = get_calling_frame();
        throw new UndefinedMethodException($name, null, $frame->caller, $frame->line);
    }
}
?>