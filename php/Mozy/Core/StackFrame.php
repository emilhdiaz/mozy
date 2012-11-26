<?php
namespace Mozy\Core;

class StackFrame extends Object implements Immutable {
    protected $file;
    protected $line;
    protected $class;
    protected $method;
    protected $type;
    protected $args;
    protected $caller;

    protected function __construct(array $frame) {
        $this->file   = clean(array_value($frame, 'file'), $_SERVER["SCRIPT_NAME"]);
        $this->line   = clean(array_value($frame, 'line'));
        $this->class  = clean(array_value($frame, 'class'));
        $this->method = clean(array_value($frame, 'function'));
        $this->type   = clean(array_value($frame, 'type'));
        $this->args   = clean(array_value($frame, 'args'));
        $this->caller = get_class_from_filename($this->file);
    }

    public function getFile() {
        return $this->file;
    }

    public function getLine() {
        return $this->line;
    }

    public function getClass() {
        return $this->class;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getType() {
        return $this->type;
    }

    public function getArguments() {
        return $this->args;
    }

    public function getCaller() {
        return $this->caller;
    }

    public function __toString() {
        return (
            $this->caller
            . ':' . $this->line . ' called '
            . $this->class
            . $this->type
            . $this->method
            . _S($this->args)
        );
    }
}
?>