<?php
namespace Mozy\Core\System;

class InternalCommand extends Command {

    protected $command;
    protected $arguments;

    protected function __construct( $command, $arguments = [] ) {
        $this->command      = $command;
        $this->arguments    = _A($arguments);
    }

    public function __invoke() {
        $localArguments = func_get_args();
        return call_user_func_array( $this->command, array_merge($this->arguments, $localArguments) );
    }
}
?>