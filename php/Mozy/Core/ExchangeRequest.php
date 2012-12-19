<?php
namespace Mozy\Core;

abstract class ExchangeRequest extends Object {
    protected $endpoint;
    protected $api;
    protected $action;
    protected $arguments;
    protected $session;

    protected function __construct($endpoint, $api, $action, $arguments = []) {
        $this->endpoint = $endpoint;
        $this->api      = $api;
        $this->action   = $action;
        $this->arguments= $arguments;
    }
}
?>