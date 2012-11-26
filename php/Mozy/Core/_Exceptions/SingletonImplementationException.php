<?php
namespace Mozy\Core;

/**
 * Thrown when a resource cannot be located either locally or over the network. 
 */ 
class SingletonImplementationException extends InvalidDefinitionException {

    protected $template = 'Class $className is a Singleton and must not declare a public constructor';
    
    public function __construct($className, Exception $previous = null, $file=null, $line=null) {
        $message = str_replace('$className', $className, $this->template);
        parent::__construct($message, $previous, $file, $line);
    }

}
?>