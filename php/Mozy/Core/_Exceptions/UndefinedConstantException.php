<?php
namespace Mozy\Core;

/**
 * Thrown when a resource cannot be located either locally or over the network. 
 */ 
class UndefinedConstantException extends InvalidUsageException {

    public function __construct($message="", Exception $previous = null, $file=null, $line=null) {
        parent::__construct($message, $previous, $file, $line);
    }
}
?>