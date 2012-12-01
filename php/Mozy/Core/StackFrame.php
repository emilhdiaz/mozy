<?php
namespace Mozy\Core;

class StackFrame extends Object {
    protected $file;
    protected $line;
    protected $object;
    protected $method;
    protected $type;
    protected $args;
    protected $caller;

    protected function __construct(array $frame) {
        $this->file   = clean(array_value($frame, 'file'), $_SERVER["SCRIPT_NAME"]);
        $this->line   = clean(array_value($frame, 'line'));
        $this->object = clean(array_value($frame, 'class'));
        $this->method = clean(array_value($frame, 'function'));
        $this->type   = clean(array_value($frame, 'type'));
        $this->args   = clean(array_value($frame, 'args'));
        $this->caller = get_class_from_filename($this->file);
    }

    public function __toString() {
        return (
            $this->caller
            . ':' . $this->line . ' called '
            . $this->object
            . $this->type
            . $this->method
            . _S($this->args)
        );
    }
}
?>