<?php
namespace Mozy\Core;

/**
 * Base class for all failures.
 */
abstract class Failure extends Exception {
    const CODE = 1000;
}
?>