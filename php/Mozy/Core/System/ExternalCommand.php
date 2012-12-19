<?php
namespace Mozy\Core\System;

class ExternalCommand extends Command {

    protected $command;
    protected $arguments;
    protected $options;

    protected function __construct( $command, $arguments = [], $options = [] ) {
        $this->command      = $command;
        $this->arguments    = _A($arguments);
        $this->options      = _A($options);
    }

    /**
     * Executes a blocking command.
     * Flags for interactive mode
     */
    public function __invoke( $interactive = false ) {
        $command = $this->command . ' ';

        // escape arguments
        foreach($this->arguments as $argument) {
            if ( !$argument ) continue;

            $command .= escapeshellarg($argument) . ' ';
        }

        // escape options
        foreach($this->options as $option => $value) {
            if ( !$option ) continue;

            $command .= escapeshellarg( '--' . $option . ' ' . $value) . ' ';
        }

        // clean command
        $command = escapeshellcmd($command);

        if( $interactive )
        	return system( $command );
        else {
        	exec( $command, $output );
        	return implode(' ', $output);
        }
    }
}
?>