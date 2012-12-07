<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\Reflection\ReflectionMethod;
use Mozy\Core\System\System;
use Mozy\Core\System\InternalCommand;

trait AsyncCallers {
    use Callers;
    /**
     * Magic Static Caller
     */
    public function __call( $name, $arguments ) {
        /* Delegate to Declared Method */
        if( method_exists($this, $name) ) {
            $method = ReflectionMethod::construct($this, $name);
            $frame = get_calling_frame();

            if( !$method->isAllowedFor($frame->caller) )
                throw new UnauthorizedMethodAccessException($name, null, $frame->caller, $frame->line);

            /* Check if there is a callback argument */
            end($arguments);
            $key = key($arguments);
            reset($arguments);
            $callback = $arguments[$key] instanceOf \Closure ? array_pop($arguments) : null;

            /* Check if this is an async method */
            if( $method->comment->annotation('async') ) {
                $command = InternalCommand::construct( $method->closure($this), $arguments );
                System::construct()->process->executeAsynchronous( $command, $callback );
                return;
            }
            /* Synchronous method call so delegate */
            else {
                $response = parent::__call($name, $arguments);
                if( $callback ) {
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