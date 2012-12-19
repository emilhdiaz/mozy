<?php
namespace Mozy\Core;

use Mozy\Core\Reflect\ReflectionMethod;

trait Getters {
    /**
     * Magic Getter
     */
    public function __get( $name ) {
        $getter = 'get' . ucfirst($name);
        $class = get_called_class();

        /* Indirect Getter Access */
        if ( method_exists($class, $getter) ) {
            $method = ReflectionMethod::construct($class, $getter);
            $caller = get_calling_class();

            // check accessibility
            if ( !$method->isAllowedFor($caller) )
                throw new UnauthorizedPropertyAccessException($name);

            return $this->$getter();
        }

        /* Default (public read access) */
        if ( !property_exists($class, $name) ) {
            throw new UndefinedPropertyException($name);
        }

        return $this->$name;
    }

    public function __isset( $name ) {
    	return isset($this->$name);
    }
}
?>