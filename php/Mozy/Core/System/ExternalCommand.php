<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Command;

class ExternalCommand extends Object implements Command {

    protected $command;
    protected $arguments;
    protected $options;

    protected function __construct( $command, $arguments = [], $options = [] ) {
        $this->command      = $command;
        $this->arguments    = _A($arguments);
        $this->options      = _A($options);
    }

    public function __toString() {
        $command = $this->command . ' ';

        // escape arguments
        foreach($this->arguments as $argument) {
            if( !$argument ) continue;

            $command .= escapeshellarg($argument) . ' ';
        }

        // escape options
        foreach($this->options as $option => $value) {
            if( !$option ) continue;

            $command .= escapeshellarg( '--' . $option . ' ' . $value) . ' ';
        }

        // clean command
        return escapeshellcmd($command);
    }

    public function __invoke() {
        exec( (string) $this, $output );
        return implode(' ', $output);
    }
}
?>