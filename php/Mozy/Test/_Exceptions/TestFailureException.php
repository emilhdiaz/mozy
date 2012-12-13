<?php
namespace Mozy\Test;

use Mozy\Core\ApplicationException;
use Mozy\Core\Exception;

/**
 * Base class for all application exceptions.
 */ 
class TestFailureException extends ApplicationException {

    protected $template = 'Assertion [$condition] has failed';

    public function __construct(Assertion $assertion, Exception $previous = null, $file=null, $line=null) {
        $message = str_replace('$condition', $assertion->condition, $this->template);
        parent::__construct($message, $previous, $file, $line);
    }

}
?>