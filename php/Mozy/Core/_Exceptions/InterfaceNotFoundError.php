<?php
namespace Mozy\Core;

/**
 * Thrown when a Interface definition cannot be located.
 */
class InterfaceNotFoundError extends ResourceNotFoundError {

	public function __construct( $interface, Exception $exception = null ) {
		parent::__construct("Interface '$interface' not found", $exception);
    }
}
?>