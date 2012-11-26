<?php
namespace Mozy\Core;

/**
 * Thrown when a resource cannot be located either locally or over the network. 
 */ 
class AssertionFailureException extends SemanticException {

    protected $assertion;

    public function __construct(Assertion $assertion) {
        parent::__construct('Failed assertion: ' .$assertion->condition);
        $this->assertion;
    }
    
    public function getAssertion() {
        $this->assertion;
    }
}
?>