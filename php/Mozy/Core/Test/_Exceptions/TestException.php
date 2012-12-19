<?php
namespace Mozy\Core\Test;

use Mozy\Core\ApplicationException;
use Mozy\Core\Exception;

/**
 * Base class for all application exceptions.
 */
class TestException extends ApplicationException {

    protected $template = 'Test $name has failed';

    public function __construct(Testable $test, Exception $previous = null, $file=null, $line=null) {
        $message = str_replace('$name', $test->name, $this->template);
        parent::__construct($message, $previous, $file, $line);
    }

}
?>