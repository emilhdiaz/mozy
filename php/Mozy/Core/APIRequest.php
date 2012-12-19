<?php
namespace Mozy\Core;

abstract class APIRequest extends Object {

    protected $api;
    protected $action;
    protected $arguments = [];
}
?>