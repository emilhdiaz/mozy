<?php
namespace Mozy\Core\Server;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

abstract class Server extends Object implements Singleton {

	const STOPPED = 0;
	const RUNNING = 1;

	protected $name;
	protected $address;
	protected $port;
	protected $admin;
	protected $root;
	protected $signature;

	abstract public function start();
	abstract public function restart();
	abstract public function stop();
}
?>