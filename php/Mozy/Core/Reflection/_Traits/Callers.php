<?php
namespace Mozy\Core\Reflection;

use Mozy\Core;

trait Callers {
    /**
     * Magic Static Caller
     */
    public function __call( $name, $arguments ) {
        /* Indirect Getter Access */
        $getter = 'get' . ucfirst($name);
        if( method_exists($this, $getter) ) {
            return call_user_func_array([$this, $getter], $arguments);
        }

        $frame = get_calling_frame();
        throw new UndefinedMethodException($name, null, $frame->caller, $frame->line);
    }
}
?>