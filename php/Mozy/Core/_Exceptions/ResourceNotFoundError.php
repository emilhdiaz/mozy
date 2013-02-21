<?php
namespace Mozy\Core;

/**
 * Thrown when a resource cannot be located either locally or over the network.
 */
class ResourceNotFoundError extends Error {

    public function __construct( $resource, Exception $previous = null ) {
    	parent::__construct("Resource '$resource' not found.", $previous);
    }
}
?>