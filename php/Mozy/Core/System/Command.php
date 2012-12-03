<?php
namespace Mozy\Core\System;

use Mozy\Core;
use Mozy\Core\Object;

class Command extends Object {

    protected $program;
    protected $arguments;
    protected $options;

    protected function __construct( $program, $arguments = [], $options = [] ) {
        $this->program      = $program;
        $this->arguments    = Core\_A($arguments);
        $this->options      = Core\_A($options);
    }

    public function __toString() {
        $command = $this->program . ' ';

        // escape arguments
        foreach($this->arguments as $argument) {
            $command .= escapeshellarg($argument) . ' ';
        }

        // escape options
        foreach($this->options as $option => $value) {
            $command .= escapeshellarg( '--' . $option . ' ' . $value) . ' ';
        }

        // clean command
        return escapeshellcmd($command);
    }
}
?>