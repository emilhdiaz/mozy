<?php
namespace Mozy\Core\System\Console;

use Mozy\Core\Server\Server;
use Mozy\Core\Exception;

class Console extends Server {

	const CSI = "\e[";
	const WIDTH = 160;

	protected $input;
	protected $output;
	protected $history;
	protected $status;
	static $debug = false;

	protected function __construct() {
		$this->input   = ConsoleInput::construct();
		$this->output  = ConsoleOutput::construct();
		$this->history = ConsoleHistory::construct();
	}

	public function start() {
		global $framework;
		$hits = 0;
		$this->clear();
		$this->output->nl();
		$this->output->line(str_repeat("#", self::WIDTH), 'bold', 'black');
		$this->output->line(' Mozy Framework - Console', 'bold', 'white');
		$this->output->line(' (c) Copywrite of Mozy Framework. All rights reserved.', 'bold', 'white');
		$this->output->line(str_repeat("#", self::WIDTH), 'bold', 'black');

		/* Start console */
		$this->status = Server::RUNNING;
		while( $this->status == Server::RUNNING ) {
			list($command, $options) = $this->input->next();
			$this->dispatch( $command, $options );

			if( $hits > 50 ) $this->stop();
		}
	}

	public function restart() {
		$this->stop();
		$this->start();
	}

	public function stop() {
		$this->output->line('Thanks for using Mozy!', 'bold', 'green');
		$this->status = Server::STOPPED;
	}

	public function dispatch( $command, $options ) {
		static $hit = 1;
		global $framework;

		try {
			switch($command) {
				case 'api':
					$this->history->add($command, $options);
					$request = APIRequest::construct($options);
					$result = $framework->callAPI($request->api, $request->action, $request->arguments);
					$view = '\Mozy\APIs\\'.$request->api.'ConsoleView';
					$view::construct($this)->{$request->action}($result);
					break;

				case 'help':
				case '?':
					HelpView::construct($this)->render();
					break;

				case 'debug':
					$this->history->add($command);
					self::$debug = true;
					break;

				case 'clear':
					$this->history->add($command);
					$this->clear();
					break;

				case 'exit':
				case 'quit':
				case 'close':
				case 'end':
				case 'q':
					$this->stop();
					break;

				case 'scrap':
					$this->history->add($command);
					include_once(MOZY_HOME . '/scrap.php');
					break;

				default:
					$this->history->add($command, $options);
					println("Invalid command '".$command."'. Type help or ? for list of valid commands.");
					break;

			}
        }
        catch(Exception $exception) {
            ExceptionView::construct($this, $exception)->render();
        }
	}

	public function clear() {
		/* Clear entire screen */
		print self::CSI.'2J';

		/* Move cursor to top left corner */
		print self::CSI.'H';
	}

    public function getPath() {
        return posix_ctermid();
    }

    public function isReadable() {
        posix_isatty(STDIN);
    }

    public function isWritable() {
        posix_isatty(STDOUT);
    }
}
?>