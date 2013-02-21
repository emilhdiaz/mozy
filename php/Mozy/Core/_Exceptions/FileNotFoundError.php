<?php
namespace Mozy\Core;

/**
 * Thrown when a File cannot be located.
 */
class FileNotFoundError extends ResourceNotFoundError {

	public function __construct( $file, Exception $exception = null ) {
		parent::__construct("File '$file' not found", $exception);
    }
}
?>