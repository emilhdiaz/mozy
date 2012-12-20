<?php
namespace Mozy\Core\System\Console;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class ConsoleHistory extends Object implements Singleton {

	protected $historyFile = '.mozy_console';
	protected $max = 20;
	protected $history = [];
	protected $pointer = 0;


	protected function __construct() {
		$historyFile = fopen(USER_HOME . $this->historyFile, 'c+');
		while( ($line = fgets($historyFile)) !== false ) {
			$this->history[] = trim($line);
			$this->pointer++;
		}
		fclose($historyFile);
	}

	public function add( $command, $options = null ) {
		$this->history[] = trim($command . ' ' . $options);
		$this->pointer = count($this->history);
	}

	public function up() {
		if ( empty($this->history) )
			return null;

		$this->pointer--;

		if ( $this->pointer < 0 )
			$this->pointer = 0;

		return $this->history[$this->pointer];
	}

	public function down() {
		if( empty($this->history) )
			return null;

		$this->pointer++;

		if ( $this->pointer >= count($this->history) ) {
			$this->pointer = count($this->history);
			return null;
		}

		return $this->history[$this->pointer];
	}

	public function __destruct() {
		$historyFile = fopen(USER_HOME . $this->historyFile, 'w');
		ftruncate($historyFile, 0);
		$history = (count($this->history) <= $this->max) ? $this->history : array_slice($this->history, -25);
		foreach( $history as $line ) {
			fwrite($historyFile, $line.PHP_EOL);
		}
		fclose($historyFile);
	}
}
?>