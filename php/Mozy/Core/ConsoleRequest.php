<?php
namespace Mozy\Core;

class ConsoleRequest extends ExchangeRequest {

    protected function __construct() {
        $command = implode(' ', $_SERVER['argv']);
        $command = explode('--', $command);

        $target  = explode(' ' , trim(array_shift($command)));
        $this->script       = array_shift($target);
        $this->api          = array_shift($target);
        $this->action       = array_shift($target);

        $this->arguments = [];
        if( count($target) == 1 )
            $this->arguments[] = $target[0];

        elseif( count($target) == 0 )
            $this->arguments[] = null;

        else
            $this->arguments[] = $target;

        foreach($command as $option) {
            $option = preg_split('/[ =:]/', trim($option));
            $optionName = camelCase(array_shift($option));

            if( count($option) == 1 )
                $this->arguments[$optionName] = $option[0];

            elseif( count($option) == 0 )
                $this->arguments[$optionName] = true;

            else
                $this->arguments[$optionName] = $option;
        }

        $this->format = array_value($this->arguments, 'format');
    }
}
?>