#!/usr/bin/env php
<?php
use Mozy\Core\Framework;
use Mozy\Core\System\Console\Console;

try {

	define('USER_HOME', getenv('HOME') . DIRECTORY_SEPARATOR);
	define('MOZY_HOME', getenv('MOZY_HOME') . DIRECTORY_SEPARATOR);
	define('NAMESPACE_SEPARATOR', '\\');
	define('PHP_TAB', "\t");
	define('DEBUG', false);
	defined('STDIN') ?: define('STDIN', fopen('php://stdin'));
	defined('STDOUT') ?: define('STDOUT', fopen('php://stdout'));
	defined('STDERR') ?: define('STDERR', fopen('php://stderr'));

	require_once(MOZY_HOME.'Mozy/common.php');
	require_once(MOZY_HOME.'Mozy/Core/Autoloader.php');
	spl_autoload_register( ['Mozy\Core\AutoLoader', 'load'], true );

	global $framework, $process;

	Framework::init();

	$console = Console::construct();
	$console->start();

}
catch(Exception $exception) {
    fwrite(STDERR, $exception . ' ' . $exception->getMessage() . PHP_EOL);
    fwrite(STDERR, $exception->getTraceAsString() . PHP_EOL);
    exit($exception->getCode());
}
?>