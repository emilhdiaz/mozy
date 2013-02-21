<?php
namespace Mozy\Core;

/**
 * Thrown when accessing an undefined class or object property.
 */
class UndefinedPropertyError extends InvalidDefinitionError {

	public function __construct( $property, $object, Exception $exception = null ) {
		parent::__construct("Property '$property' has not been defined for '$object'", $exception);
    }
}
?>