<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class Console extends Object implements Singleton {

    public function getPath() {
        return posix_ctermid();
    }

    public function isReadable() {
        posix_isatty(STDIN);
    }

    public function isWritable() {
        posix_isatty(STDOUT);
    }

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
        return Output::construct();
    }
}
?>