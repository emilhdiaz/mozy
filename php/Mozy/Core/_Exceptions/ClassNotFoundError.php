<?php
namespace Mozy\Core;

/**
 * Thrown when a Class definition cannot be located.
 */
class ClassNotFoundError extends ResourceNotFoundError {

	public function __construct( $class, Exception $exception = null ) {
		parent::__construct("Class '$class' not found", $exception);
    }
}
?>