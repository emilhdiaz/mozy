<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionMethod;

trait Getters {
    /**
     * Magic Getter
     */
    public function __get( $name ) {
        $getter = 'get' . ucfirst($name);
        $class = get_called_class();

        /* Indirect Getter Access */
        if( method_exists($class, $getter) ) {
            $method = ReflectionMethod::construct($class, $getter);
            $frame = get_calling_frame();

            // check accessibility
            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedPropertyAccessException($name, null, $frame->caller, $frame->line);

            return $this->$getter();
        }

        /* Default (public read access) */
        if( !property_exists($class, $name) ) {
            $frame = get_calling_frame();
            debug($name);
            throw new UndefinedPropertyException($name, null, $frame->caller, $frame->line);
        }

        return $this->$name;
    }
}
?>