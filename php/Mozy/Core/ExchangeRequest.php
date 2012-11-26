<?php
namespace Mozy\Core;

abstract class ExchangeRequest extends Object {
    protected $script;
    protected $api;
    protected $action;
    protected $arguments;
    protected $session;
    protected $format;
}
?>