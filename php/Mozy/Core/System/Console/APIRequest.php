<?php
namespace Mozy\Core\System\Console;

class APIRequest extends \Mozy\Core\APIRequest {

    protected function __construct( $request ) {
       	$parts = explode('--', $request);
        $target	 = explode(' ' , trim(array_shift($parts)));

        $this->api        = array_shift($target);
        $this->action     = array_shift($target);

        $arguments  = [];
        if ( count($target) == 1 )
            $arguments[] = $target[0];

        elseif ( count($target) == 0 )
            $arguments[] = null;

        else
            $arguments[] = $target;

        foreach($parts as $option) {
            $option = preg_split('/[ =]/', trim($option));
            $optionName = camelCase(array_shift($option));

            if ( count($option) == 1 )
                $arguments[$optionName] = $option[0];

            elseif ( count($option) == 0 )
                $arguments[$optionName] = true;

            else
                $arguments[$optionName] = $option;
        }

        $this->arguments = $arguments;
    }
}
?>