<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;
use Mozy\Core\System\System;
use Mozy\Core\System\Command;

trait AsyncCallers {
    use Callers;
    /**
     * Magic Static Caller
     */
    public function __call( $name, $arguments ) {
        /* Delegate to Declared Method */
        if ( method_exists($this, $name) ) {
            $method = ReflectionMethod::construct($this, $name);
            $caller = get_calling_class();

            if ( !$method->isAllowedFor($caller) )
                throw new UnauthorizedMethodAccessException($name);

            /* Check if there is a callback argument */
            end($arguments);
            $key = key($arguments);
            reset($arguments);
            $callback = $arguments[$key] instanceOf \Closure ? array_pop($arguments) : null;

            /* Check if this is an async method */
            if ( $method->comment->annotation('async') ) {
                $command = Command::construct( $method->closure($this), $arguments );
                System::construct()->process->executeAsynchronous( $command, $callback );
                return;
            }
            /* Synchronous method call so delegate */
            else {
                $response = parent::__call($name, $arguments);
                if ( $callback ) {
                    return call_user_func_array( $callback, _A($response) );
                } else {
                    return $response;
                }
            }
        }

        parent::__call( $name, $arguments );
    }
}
?>