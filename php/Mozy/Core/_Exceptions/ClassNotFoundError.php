<?php
namespace Mozy\Core;

/**
 * Thrown when a Class file cannot be located.
 */
class ClassNotFoundError extends ResourceNotFoundError {
    const CODE = 2110;
    const REGEX = '/^Class \'\S+\' not found.*/';
}
?>