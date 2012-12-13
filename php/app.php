#!/usr/bin/env php
<?php
use Mozy\Core\Framework;
use Mozy\Core\SourceParser;

require_once('Mozy/Core/Framework.php');
Framework::init();

#require_once('php_error/php_error.php');
#php_error\reportErrors();

#Unknown::construct(1, 2);

$framework->processExchange();
?>