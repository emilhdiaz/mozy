<?php
namespace Mozy\Core;

abstract class ExchangeRequest extends Object implements Singleton {
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
}
?>