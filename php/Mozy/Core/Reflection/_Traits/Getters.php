<?php
namespace Mozy\Core\Reflection;

use Mozy\Core;

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
            $frame = get_calling_frame();
            throw new UndefinedPropertyException($name, null, $frame->caller, $frame->line);
        }

        return $this->$name;
    }
}
?>