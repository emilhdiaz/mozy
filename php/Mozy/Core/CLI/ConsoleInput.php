<?php
namespace Mozy\Core\CLI;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class ConsoleInput extends Object implements Singleton {

	const PROMPT = "mozy > ";
	protected $input;
	protected $column;
	protected $index;
	protected $history;
	protected $scrolling = 0;

	protected function __construct() {
		$this->history = ConsoleHistory::construct();
		system('stty sane');
		system('stty -icanon -echo');
		system('stty erase ^?');
	}

    public function next() {
    	global $process;
		$in = $process->in;
		$in->setBlocking(true);

		$this->reset();

		while( ($char = $process->in->readChar()) !== false ) {

			switch( ord($char) ) {
				case 10:						// new line
				case 13: 						// carriage return
					return $this->done();
					break;

				case 127:						// delete
					$this->back();
					break;

				case 27:						// escape
					$in->setBlocking(false);
					$crt = $in->read();
					switch($crt) {
						case '[A':
							$this->up();
							break;

						case '[B':
							$this->down();
							break;

						case '[C':
							$this->right();
							break;

						case '[D':
							$this->left();
							break;

						case '[5~':
#							$this->pageUp();
							break;

						case '[6~':
#							$this->pageDown();
							break;

						default:
							println('Unhandled control sequence:' .$crt);
					}
					$in->setBlocking(true);
					break;

				default:						// any other char
					$this->add($char);
					break;
			}
		}
		return $this;
    }

	public function reset() {
		$this->input = '';
		print PHP_EOL;
		$this->prompt();
	}

    public function done() {
		/* Adjust Screen */
		print PHP_EOL;
		print PHP_EOL;
		return $this->input;
    }

    public function add($char) {
		/* Adjust internal input buffer */
    	$this->input = substr_replace($this->input, $char, $this->index, 0);

		/* Adjust screen */
    	print Console::CSI.'K';
    	print substr($this->input, $this->index);
    	$this->sync();

		/* Move to next position */
		$this->right();
    }

    public function back() {
    	/* Move to previous position */
    	$this->left();

		/* Adjust internal input buffer */
		$this->input = substr_replace($this->input, '', $this->index, 1);

    	/* Adjust screen */
		print Console::CSI.'K';
		print substr($this->input, $this->index);
		$this->sync();
	}

	public function sync() {
		if( $this->scrolling > 0 )
			$this->pageDown($this->scrolling);
		if( $this->scrolling < 0 )
			$this->pageUp($this->scrolling);

		$this->scrolling = 0;

		print Console::CSI.$this->column.'G';
		print Console::CSI.'?25h';
	}

    public function right() {
    	if( $this->index < strlen($this->input) ) {
			$this->index++;
			$this->column++;
			$this->sync();
    	}
    }

    public function left() {
    	if( $this->index > 0 ) {
	    	$this->index--;
    		$this->column--;
    		$this->sync();
    	}
    }

    public function up() {
    	$this->input = $this->history->up();
    	$this->refresh();
    }

    public function down() {
    	$this->input = $this->history->down();
    	$this->refresh();
    }

    public function pageUp( $lines = 5 ) {
    	$this->scrolling += $lines;
    	print Console::CSI.'?25l';
    	print Console::CSI.$lines.'T';
    }

    public function pageDown( $lines = 5 ) {
    	$this->scrolling -= $lines;
    	print Console::CSI.'?25l';
    	print Console::CSI.$lines.'S';
    }

	public function refresh() {
		/* Adjust screen */
		$this->prompt();
		print $this->input;

		$this->index = strlen($this->input);
		$this->column += $this->index;
		$this->sync();
	}

	public function prompt() {
		print Console::CSI.'1G';
		print Console::CSI.'K';
		print Console::CSI.'01;32m';
		print self::PROMPT;
		print Console::CSI.'m';
		$this->column = strlen(self::PROMPT) + 1;
		$this->index = 0;
		$this->scrolling = 0;
		$this->sync();
	}

    public function __destruct() {
    	system('stty sane');
    }
}
?>