<?php
namespace Mozy\Core\Reflection; 

class ReflectionParameter extends \ReflectionParameter {

    public $declaringClass;
    public $method;
    
    public function __construct($class, $method, $parameter) {
        parent::__construct([$class, $method], $parameter);
        $this->declaringClass = $class;
        $this->method = $method;
    }
    
    public function getDeclaringClass() {
        return new ReflectionClass($this->declaringClass);
    }
    
    public function getDeclaringMethod() {
        return new ReflectionMethod($this->declaringClass, $this->method);
    }
    
    public function getType() {
        $className;
        $class = parent::getClass();
        
        // check annotations
        if( !$class ) {
            $comment = $this->getDeclaringMethod()->getDocComment();
            preg_match('/@var\s+'.$this->name.'\s+(\S+)[\r\n]/', $comment, $matches);
            $className = count($matches) > 0 ? $matches[1] : null;
        } 
        else {
            $className = $class->name;
        }
        
        if( !ReflectionClass::exists($className) ) 
            return;
        
        return new ReflectionClass($className);
    }
}
?>