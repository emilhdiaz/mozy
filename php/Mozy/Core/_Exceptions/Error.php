<?php
namespace Mozy\Core;

/**
 * Base class for all errors.
 * Typically these represent programming or logic errors than can be
 * avoided with corrections to the source code. These errors are similar
 * to the concept of unchecked exceptions in that they should not be caught
 * but rather fix in source code.
 */
abstract class Error extends Exception {}
?>