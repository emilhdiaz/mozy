<?php
namespace Mozy\Core\System\Console;

use Mozy\Core\Exception;
use Mozy\Core\SourceParser;

class ExceptionView extends ConsoleView {

	protected $exception;

	protected function __construct(Console $console, Exception $exception) {
		$this->console = $console;
		$this->exception = $exception;
	}

	public function render() {
		global $process;

		$process->out->buffer()->clean();

    	$output = $this->console->output;
    	$output->enableSourceHighlighting();

		$callerColumnSize 	= 20;
		$lineColumnSize 	= 5;
		$sourceColumnSize 	= 130;

    	$parser	= SourceParser::construct();

		$exception = $this->exception;

		$output->nl();
        $output->line($exception->name, 'bold', 'red', 0, true);
        $output->line($exception->message, 'bold', 'yellow', 0, true);
        $output->nl();
        $output->line("Stack Trace:", 'bold', 'green', 0, true);

		foreach( $exception->stackTrace->frames as $i=>$frame ) {
			if( $frame->file ) {
				$caller = get_short_class($frame->caller) ?: get_short_file($frame->file);
				$source = $parser->parse($frame->file);

				$output->line(str_repeat('_', $callerColumnSize + $lineColumnSize + $sourceColumnSize + 2), 'bold', 'white', 0, true);
				$output->column("[$i] " . $caller, $callerColumnSize, -1, 'bold', 'white', 'black');
				$output->column(':' . $frame->line, $lineColumnSize, -1, 'bold', 'white', 'black');
				$output->column( ' ' . trim($source[$frame->line]), $sourceColumnSize+2, -1, 'normal', 'white', 'black');
				$output->nl();
			}

			$source = get_short_class($frame->class) . $frame->type . $frame->method . argument_string($frame->args);
			$output->column('', $callerColumnSize + $lineColumnSize, -1, 'normal', 'white', 'black');
			$output->column(' -> ' . $source, $sourceColumnSize+2, -1, 'normal', 'white', 'black');
			$output->nl();
		}
		$output->nl();


		$source = $parser->parse($exception->file);
    	$span = 8;
    	$lines = (count($source));
    	$min = ($exception->line > $span) ? $exception->line - $span : 0;
    	$max = ($exception->line < ($lines - $span)) ? $exception->line + $span : $lines;
		$sourceColumnSize += $callerColumnSize;
		$sourceColumnSize -= $lineColumnSize;

		$output->line("Source: " . $exception->shortFile . " (lines [$min-$max] of $lines)", 'bold', 'green', 0, true);
		$output->line(str_repeat('-', $lineColumnSize + $sourceColumnSize + $lineColumnSize + 2), 'bold', 'black', 0, true);
		for( $i = $min; $i <= $max; $i++ ) {
			$output->column("|", 1, -1, 'bold', 'white', 'black', true);
			$lineSource = ' ' . $source[$i];

			if ( $i == $exception->line ) {
				$output->column($i, $lineColumnSize, -1, 'bold', 'black', 'white');
				$output->column($lineSource, $sourceColumnSize, -1, 'bold', 'black', 'white');
				$output->column($i, $lineColumnSize, 1, 'bold', 'black', 'white');
			}
			else {
				$output->column($i, $lineColumnSize, -1, 'normal', 'white', 'black');
				$output->column( $lineSource, $sourceColumnSize, -1, 'normal', 'white', 'black');
				$output->column($i, $lineColumnSize, 1, 'normal', 'white', 'black');
			}

			$output->column("|", 1, -1, 'bold', 'white', 'black', true);
			$output->nl();
		}
		$output->line(str_repeat('-', $lineColumnSize + $sourceColumnSize + $lineColumnSize + 2), 'bold', 'black', 0, true);
		$output->nl();

		$process->out->flush()->end();
	}
}
?>