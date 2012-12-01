<?php
namespace Mozy\Core;

class StackTrace extends Object {

    protected $stack;
    protected $pointer = 0;

    protected function __construct( Exception $e = null ) {
        $this->stack = $e ? $e->trace : debug_backtrace();

        if( empty($this->stack) )
            $this->stack[0] = [];
    }

    public function getCurrentFrame() {
        return StackFrame::construct($this->stack[$this->pointer]);
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
        return (bool) ( $this->pointer < (sizeof($this->stack) - 1) );
    }

    public function getPreviousFrame() {
        $this->pointer--;
        return $this->currentFrame;
    }

    public function __toString() {
        $str = "    ". $this->currentFrame ."\n";
        while ($this->hasNextFrame()) {
            $str .= "    ". $this->nextFrame ."\n";
        }
        return $str;
    }
}
?>