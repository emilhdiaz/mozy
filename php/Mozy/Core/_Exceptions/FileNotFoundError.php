<?php
namespace Mozy\Core;

/**
 * Thrown when a File cannot be located.
 */
class FileNotFoundError extends ResourceNotFoundError {
    const CODE = 2110;
}
?>