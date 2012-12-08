<?php
namespace Mozy\Core;

/**
 * Base class for all application exceptions.
 */
abstract class ApplicationException extends Exception {

    public function __construct($message="", Exception $previous = null, $file=null, $line=null) {
        parent::__construct($message, $previous, $file, $line);
    }

}
?>