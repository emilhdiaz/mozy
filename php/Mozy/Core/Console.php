<?php
namespace Mozy\Core;

class Console extends Object {

    public function command($endpoint, $api, $action, $arguments = [], $format = 'native') {
        $command = 'php '. $endpoint . ' ' . $api . ' ' . $action . ' ';

        $prefix = '--';
        $glue = ' ';

        foreach($arguments as $key=>$value) {
            // check if string key
            $command .= (is_string($key) ? $prefix . $key . $glue : '');


            #TODO add switch on format for serializing objects
            // check if value is array
            $command .= (is_array($value) ? implode_assoc($value, $glue) :  "'". serialize($value) . "'");

            $command .= ' ';
        }

        $command .= $prefix . 'format' . $glue . $format;

        return $command;
    }

    public function getOutput() {
        return ConsoleOutput::construct();
    }
}
?>