<?php
namespace Mozy\Core;

class StackFrame extends Object {
    protected $file;
    protected $line;
    protected $class;
    protected $method;
    protected $type;
    protected $args;
    protected $caller;
    protected $previous;

    protected function __construct(array $frame, StackFrame $previous = null) {
        $this->file   	= clean(array_value($frame, 'file'), $_SERVER["SCRIPT_NAME"]);
        $this->line   	= clean(array_value($frame, 'line')) ?: ($previous ? $previous->line : null);
        $this->class 	= clean(array_value($frame, 'class'));
        $this->method 	= clean(array_value($frame, 'function'));
        $this->type   	= clean(array_value($frame, 'type'));
        $this->args   	= clean(array_value($frame, 'args'));
        $this->caller 	= get_class_from_filename($this->file) ?: substr($this->file, strrpos ($this->file, DIRECTORY_SEPARATOR)+1);
        $this->previous = $previous;
    }

    public function __toString() {
    	$snippetSize = 40;
        $padding = strlen($this->caller) < $snippetSize ? $snippetSize - strlen($this->caller) : 0;
        return (
            $this->line . "\t"
            . $this->caller . str_repeat(" ", $padding)
            . $this->class
            . $this->type
            . $this->method
#            . _S($this->args)
        );
    }
}
?>