<?php
namespace Mozy\Core;

class ConsoleRequest extends ExchangeRequest {

    protected function __construct() {
        $command = implode(' ', $_SERVER['argv']);
        $command = explode('--', $command);

        $target     = explode(' ' , trim(array_shift($command)));
        $endpoint   = array_shift($target);
        $api        = array_shift($target);
        $action     = array_shift($target);

        $arguments  = [];
        if( count($target) == 1 )
            $arguments[] = $target[0];

        elseif( count($target) == 0 )
            $arguments[] = null;

        else
            $arguments[] = $target;

        foreach($command as $option) {
            $option = preg_split('/[ =]/', trim($option));
            $optionName = camelCase(array_shift($option));

            if( count($option) == 1 )
                $arguments[$optionName] = $option[0];

            elseif( count($option) == 0 )
                $arguments[$optionName] = true;

            else
                $arguments[$optionName] = $option;
        }

        if( $format = array_value($arguments, 'format') )
            unset($arguments['format']);
        else
            $format = 'console';

#        $arguments = convert($arguments, $format, 'native');

        parent::__construct($endpoint, $api, $action, $arguments, $format);
    }
}
?>