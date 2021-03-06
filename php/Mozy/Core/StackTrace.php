<?php
namespace Mozy\Core;

class StackTrace extends Object {

    protected $frames = [];
    protected $pointer = 0;

    protected function __construct( Exception $e = null ) {
    	if ( !$e ) {
    		$stack = debug_backtrace();
    		array_shift($stack);
    	}
    	else {
    		$stack = $e->trace;
    	}
    	$stack = array_reverse($stack);

		$frames = [];
		$previous = null;
        foreach($stack as $trace) {
        	$frame = StackFrame::construct($trace, $previous);
        	$frames[] = $frame;
        	$previous = $frame;
        }

		$this->frames = $frames;
#        $this->frames = array_reverse($frames);
    }

    public function getCurrentFrame() {
        return $this->frames[$this->pointer];
    }

    public function getTopFrame() {
        $this->pointer = 0;
        return $this->currentFrame;
    }

    public function getNextFrame() {
        $this->pointer++;
        return $this->currentFrame;
    }

    public function hasNextFrame() {
        return (bool) ( $this->pointer < (sizeof($this->frames) - 1) );
    }

    public function getPreviousFrame() {
        $this->pointer--;
        return $this->currentFrame;
    }
}
?>