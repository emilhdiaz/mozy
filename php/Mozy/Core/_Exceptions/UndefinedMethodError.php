<?php
namespace Mozy\Core;

/**
 * Thrown when calling an undefined class or object method.
 */
class UndefinedMethodError extends InvalidDefinitionError {

	public function __construct( $method, $object, Exception $exception = null ) {
		parent::__construct("Method '$method' has not been defined for '$object'", $exception);
    }
}
?>