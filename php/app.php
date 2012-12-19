#!/usr/bin/env php
<?php
use Mozy\Core\Framework;
use Mozy\Core\CLI\Console;

require_once('Mozy/Core/Framework.php');
Framework::init();

$console = Console::construct();

$console->start();
?>