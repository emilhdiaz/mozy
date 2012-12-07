#!/usr/bin/env php
<?php
use Mozy\Core\Framework;

require_once('Mozy/Core/Framework.php');

Framework::init();

$framework->processExchange();
?>