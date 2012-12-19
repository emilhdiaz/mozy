<?php
namespace Mozy\Core\CLI;

use Mozy\Core\Object;
use Mozy\Core\Singleton;
use Mozy\Core\Exception;

class Console extends Object implements Singleton {

	const CSI = "\e[";

	protected $in;
	protected $out;
	protected $history;

	protected function __construct() {
		$this->in = ConsoleInput::construct();
		$this->out = ConsoleOutput::construct();
		$this->history = ConsoleHistory::construct();
	}

	public function start() {
		$this->clear();
		println('Mozy Console - (c) Mozy Framework');

		/* Start console */
		while( $this->dispatch( $this->in->next() ) );
	}

	public function dispatch( $input ) {
		static $hit = 1;
		global $framework;

		$command = @array_shift(explode(" ", $input));

		try {
			switch($command) {
				case 'api':
					$this->history->add($input);
					$request = ExchangeRequest::construct($input);
					$result = $framework->callAPI($request->api, $request->action, $request->arguments);
					$view = '\Mozy\APIs\\'.$request->api.'CLIView';
					$view::construct()->{$request->action}($result);
					return true;
					break;

				case 'help':
				case '?':
					HelpView::construct()->render();
					return true;
					break;

				case 'clear':
					$this->clear();
					return true;
					break;

				case 'exit':
				case 'quit':
				case 'close':
				case 'end':
				case 'q':
					println('Goodbye!');
					return false;
					break;

				default:
					$this->history->add($input);
					println("Invalid command '".$command."'. Type help or ? for list of valid commands.");
					return true;
					break;

			}
        }
        catch(Exception $exception) {
            ExceptionView::construct($exception)->render();
            return true;
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
