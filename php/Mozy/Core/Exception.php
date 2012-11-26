<?php
namespace Mozy\Core;

class Exception extends \Exception {
    
    const ClassNotFoundRegex            = '/^Class \'\S+\' not found.*/';
    const InterfaceNotFoundRegex        = '/^Interface \'\S+\' not found.*/';
    const TraitNotFoundRegex            = '/^Trait \'\S+\' not found.*/';
    
    const AbstractDefinitionRegex       = '/^Class \S+ contains \d{1,} abstract methods? and must therefore be declared abstract.*/';
    const MissingImplementationRegex    = '/^Non-abstract method \S+ must contain body.*/';
    
    const UndefinedMethodRegex          = '/^Call to undefined method.*/';
    const UndefinedPropertyRegex        = '/^Undefined property.*/';
    const UndefinedConstantRegex        = '/[Uu]ndefined constant.*/';
    
    const UnauthorizedMethodAccessRegex = '/^Call to (private|protected) method.*/';
    const UnauthorizedPropertyAccessRegex = '/^Cannot access (private|protected) property.*/';
    const InvalidConstructionRegex      = '/^Call to (private|protected) (\S+)::__construct().*/';
    
    const MissingArgumentRegex          = '/^Argument \d{1,} passed to \S+ must be an instance of \S+ none given.*/';
    const MissingArgument2Regex         = '/^Missing argument \d{1,} for .*/';
    const InvalidArgumentTypeRegex      = '/^Argument \d{1,} passed to \S+ must be an instance of \S+ (?!none)\S+ given.*/';
    
    const NullReferenceRegex            = '/^Undefined variable.*/';
    const NullDereferenceRegex          = '/^Trying to get property of non-object.*/';
    const NullDereference2Regex         = '/^Call to a member function \S+ on a non-object.*/';
    const InvalidArrayKeyRegex          = '/^Undefined offset.*/';
    const InvalidStringOffsetRegex      = '/^Uninitialized string offset.*/';
    
    const DivisionByZeroRegex           = '/^Division by zero.*/';
    
    public $name;
    protected $stackTrace; 

    public function __construct($message="", $code=0, Exception $previous = null, $file=null, $line=null) {
        parent::__construct($message, $code, $previous);
        $this->file = $file ?: $this->file;
        $this->line = $line ?: $this->line;
        $this->name = get_called_class();
        try{
            $this->stackTrace = StackTrace::construct($this);
        } catch ( \Exception $e ) {
            die('Exception handling is misbehaving, check the Exception Class!');
        }
    }
            
    public function getClass() {
        return $this->stackTrace->getTopFrame()->getClass();
    }
    
    public function getMethod() {
        return $this->stackTrace->getTopFrame()->getMethod();
    }
    
    public function getArguments() {
        return $this->stackTrace->getTopFrame()->getArguments();
    }
    
    public function getStackTrace() {
        return $this->stackTrace;
    }
    
    public function __toString() {
        return (
            get_called_class() 
            . ': ' .$this->message
            . ' by ' . get_class_from_filename($this->file)
            . ':' .$this->line
        );
    }
}

?>