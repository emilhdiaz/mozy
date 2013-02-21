<?php
namespace Mozy\Core;

/**
 * Thrown when a Trait definition cannot be located.
 */
class TraitNotFoundError extends ResourceNotFoundError {

	public function __construct( $trait, Exception $exception = null ) {
		parent::__construct("Trait '$trait' not found", $exception);
    }
}
?>