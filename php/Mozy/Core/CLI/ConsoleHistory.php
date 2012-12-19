<?php
namespace Mozy\Core\CLI;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class ConsoleHistory extends Object implements Singleton {

	const HISTORY_FILE = '.console_history';
	const MAX = 20;
	protected $history = [];
	protected $pointer = 0;


	protected function __construct() {
		$historyFile = fopen(self::HISTORY_FILE, 'c+');
		while( ($line = fgets($historyFile)) !== false ) {
			$this->history[] = trim($line);
			$this->pointer++;
		}
		fclose($historyFile);
	}

	public function add( $input ) {
		$this->history[] = trim(_S($input));
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
		$historyFile = fopen(self::HISTORY_FILE, 'w');
		ftruncate($historyFile, 0);
		$history = (count($this->history) <= self::MAX) ? $this->history : array_slice($this->history, -25);
		foreach( $history as $line ) {
			fwrite($historyFile, $line.PHP_EOL);
		}
		fclose($historyFile);
	}
}
?>