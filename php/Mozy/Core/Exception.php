<?php
namespace Mozy\Core;

class Exception extends \Exception {
    use Getters;

    const InvalidConstructionRegex      = '/^Call to (private|protected) (\S+)::__construct().*/';

    const MissingArgumentRegex          = '/^Argument \d{1,} passed to \S+ must be an instance of \S+ none given.*/';
    const MissingArgument2Regex         = '/^Missing argument \d{1,} for .*/';

    const NullReferenceRegex            = '/^Undefined variable.*/';
    const NullDereferenceRegex          = '/^Trying to get property of non-object.*/';
    const NullDereference2Regex         = '/^Call to a member function \S+ on a non-object.*/';
    const InvalidArrayKeyRegex          = '/^Undefined offset.*/';
    const InvalidStringOffsetRegex      = '/^Uninitialized string offset.*/';

    const DivisionByZeroRegex           = '/^Division by zero.*/';

    public $name;
    protected $stackTrace;
    protected static $exceptionCode;

    public function __construct($message="", Exception $previous = null, $file=null, $line=null) {
        parent::__construct($message, 0, $previous);
        $this->file = $file ?: $this->file;
        $this->line = $line ?: $this->line;
        $this->name = get_called_class();
        try{
            $this->stackTrace = StackTrace::construct($this);
        } catch ( \Exception $e ) {
            die('Exception handling is misbehaving, check the Exception Class!');
        }
    }

	public function getShortFile() {
		return substr($this->file, strrpos($this->file, DIRECTORY_SEPARATOR));
	}

    public function getClass() {
        return $this->stackTrace->topFrame->class;
    }

    public function getMethod() {
        return $this->stackTrace->topFrame->method;
    }

    public function getArguments() {
        return $this->stackTrace->topFrame->arguments;
    }

    public function getCaller() {
    	return $this->stackTrace->topFrame->previous->class;
    }

    public function getStackTrace() {
        return $this->stackTrace;
    }

    public function __sleep() {
        return ['name', 'message', 'code', 'file', 'line'];
    }

    public function __toString() {
    	return $this->name;
    }

    public function copy() {
        return new static($this->message, $this, $this->file, $this->line);
    }
}

?>