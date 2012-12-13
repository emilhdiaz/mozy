<?php
namespace Mozy\Core\Reflection;

trait Getters {
    /**
     * Magic Getter
     */
    public function __get( $name ) {
        $getter = 'get' . ucfirst($name);
        $class = get_called_class();

        /* Indirect Getter Access */
        if( method_exists($class, $getter) )
            return $this->$getter();

        /* Default (public read access) */
        if( !property_exists($class, $name) ) {
            throw new UndefinedPropertyException($name);
        }

        return $this->$name;
    }
}
?>