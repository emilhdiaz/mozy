<?php
namespace Mozy\Core\Reflect;

trait Callers {
    /**
     * Magic Static Caller
     */
    public function __call( $name, $arguments ) {
        /* Indirect Getter Access */
        $getter = 'get' . ucfirst($name);
        if ( method_exists($this, $getter) ) {
            return call_user_func_array([$this, $getter], $arguments);
        }

        throw new UndefinedMethodError($name, $this);
    }
}
?>