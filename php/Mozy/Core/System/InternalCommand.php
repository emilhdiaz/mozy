<?php
namespace Mozy\Core\System;

use Mozy\Core;
use Mozy\Core\Object;
use Mozy\Core\Command;

class InternalCommand extends Object implements Command {

    protected $command;
    protected $arguments;

    protected function __construct( $command, $arguments = [] ) {
        $this->command      = $command;
        $this->arguments    = Core\_A($arguments);
    }

    public function __toString() {
        return $this->commmand . Core\_S($this->arguments);
    }

    public function __invoke() {
        $streams = func_get_args();
        return call_user_func_array($this->command, array_merge($streams, $this->arguments) );
    }
}
?>