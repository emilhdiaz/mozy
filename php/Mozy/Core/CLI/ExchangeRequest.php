<?php
namespace Mozy\Core\CLI;

class ExchangeRequest extends \Mozy\Core\ExchangeRequest {

    protected function __construct( $input ) {
       	$command = explode('--', $input);

        $target     = explode(' ' , trim(array_shift($command)));
        $endpoint   = array_shift($target);
        $api        = array_shift($target);
        $action     = array_shift($target);

        $arguments  = [];
        if ( count($target) == 1 )
            $arguments[] = $target[0];

        elseif ( count($target) == 0 )
            $arguments[] = null;

        else
            $arguments[] = $target;

        foreach($command as $option) {
            $option = preg_split('/[ =]/', trim($option));
            $optionName = camelCase(array_shift($option));

            if ( count($option) == 1 )
                $arguments[$optionName] = $option[0];

            elseif ( count($option) == 0 )
                $arguments[$optionName] = true;

            else
                $arguments[$optionName] = $option;
        }

        parent::__construct($endpoint, $api, $action, $arguments);
    }
}
?>