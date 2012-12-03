<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionMethod;

trait Setters {
    /**
     * Magic Setter.
     */
    public function __set( $name, $value ) {
        $setter = 'set' . ucfirst($name);
        $class = get_called_class();

        /* Indirect Getter Access */
        if( method_exists($class, $setter) ) {
            $method = ReflectionMethod::construct($class, $setter);
            $frame = get_calling_frame();

            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedPropertyAccessException($name, null, $frame->caller, $frame->line);

            return $this->$setter($value);
        }

        /* Default private write access */
        if( !property_exists($class, $name) ) {
            $frame = get_calling_frame();
            throw new UndefinedPropertyException($name, null, $frame->caller, $frame->line);
        }

        $frame = get_calling_frame();
        throw new UnauthorizedPropertyAccessException($name, null, $frame->caller, $frame->line);
    }
}
?>