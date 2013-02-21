<?php
namespace Mozy\Core;

use Mozy\Core\Reflect\ReflectionMethod;

trait Setters {
    /**
     * Magic Setter.
     */
    public function __set( $name, $value ) {
        $setter = 'set' . ucfirst($name);
        $class = get_called_class();

        /* Indirect Getter Access */
        if ( method_exists($class, $setter) ) {
            $method = ReflectionMethod::construct($class, $setter);
            $caller = get_calling_class();

            if ( !$method->isAllowedFor($caller) )
                throw new UnauthorizedPropertyAccessException($name);

            return $this->$setter($value);
        }

        /* Default private write access */
        if ( !property_exists($class, $name) ) {
            throw new UndefinedPropertyError($name, $class);
        }

        throw new UnauthorizedPropertyAccessException($name);
    }
}
?>