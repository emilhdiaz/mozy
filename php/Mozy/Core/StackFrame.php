<?php
namespace Mozy\Core;

class StackFrame extends Object {
    protected $file;
    protected $line;
    protected $class;
    protected $sClass;
    protected $method;
    protected $type;
    protected $args;
    protected $caller;
    protected $previous;

    protected function __construct(array $frame, StackFrame $previous = null) {
        $this->file   		= clean(array_value($frame, 'file'));
        $this->line   		= clean(array_value($frame, 'line'));
        $this->class 		= clean(array_value($frame, 'class'));
        $this->method 		= clean(array_value($frame, 'function'));
        $this->type   		= clean(array_value($frame, 'type'));
        $this->args   		= clean(array_value($frame, 'args'));
        $this->caller 		= get_class_from_filename($this->file);
        $this->previous 	= $previous;
    }
}
?>