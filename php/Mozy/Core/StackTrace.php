<?php
namespace Mozy\Core;

class StackTrace extends Object implements Immutable {
    
    protected $stack;
    protected $pointer = 0;
    
    protected function __construct( Exception $e = null ) {
        $this->stack = $e ? $e->getTrace() : debug_backtrace();
        
        if( empty($this->stack) ) 
            $this->stack[0] = [];
    }
    
    public function getCurrentFrame() {        
        return StackFrame::construct($this->stack[$this->pointer]);
    }

    public function getTopFrame() {
        $this->pointer = 0;
        return $this->getCurrentFrame();
    }
    
    public function getNextFrame() {
        $this->pointer++;
        return $this->getCurrentFrame();
    }
    
    public function hasNextFrame() {
        return (bool) ( $this->pointer < (sizeof($this->stack) - 1) );
    }
    
    public function getPreviousFrame() {
        $this->pointer--;
        return $this->getCurrentFrame();
    }
    
    public function __toString() {
        $str = "    ". $this->getCurrentFrame() ."\n";
        while ($this->hasNextFrame()) {
            $str .= "    ". $this->getNextFrame() ."\n";
        }
        return $str;
    }
}
?>