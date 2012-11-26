<?php
namespace Mozy\Core;

/**
 * Base class for all semantic exceptions.
 */ 
abstract class SemanticException extends Exception {

    public function __construct($message="", Exception $previous = null, $file=null, $line=null) {
        parent::__construct($message, E_USER_ERROR, $previous, $file, $line);
    }

}
?>