<?php
namespace Mozy\Core;

/**
 * Thrown when a resource cannot be located either locally or over the network. 
 */ 
class InvalidConstructionException extends SemanticException {

    protected $template = 'Class $className must be instanciated through its static constructor';

    public function __construct($message="", Exception $previous = null, $file=null, $line=null) {
        preg_match(Exception::InvalidConstructionRegex, $message, $matches);
        $message = str_replace('$className', $matches[2], $this->template);
        parent::__construct($message, $previous, $file, $line);
    }
}
?>