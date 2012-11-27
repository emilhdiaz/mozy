<?php
namespace Mozy\Core;

abstract class ExchangeRequest extends Object {
    protected $endpoint;
    protected $api;
    protected $action;
    protected $arguments;
    protected $session;
    protected $format;

    protected function __construct($endpoint, $api, $action, $arguments = [], $format = null) {
        $this->endpoint = $endpoint;
        $this->api      = $api;
        $this->action   = $action;
        $this->arguments= $arguments;
        $this->format   = $format;
    }

    #TODO move method to more appropriate class
    protected static function convert(&$data, $from, $to = 'native') {
        if( !is_array($data) ) {
            // check format and convert
            switch($from) {
                case 'serialized':
                    $data = unserialize($data);
                    break;
            }
        }
        else {
            foreach($data as &$value) {
                self::convert($value, $from, $to);
            }
        }
    }

    abstract public function send();

    public static function current() {

    }
}
?>