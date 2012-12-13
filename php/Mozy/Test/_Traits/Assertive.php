<?php
namespace Mozy\Test;

trait Assertive {
    protected $record = true;
    protected $assertions = [];

    public static function __callStatic( $name, $arguments ) {
        return parent::__callStatic($name, $arguments);
    }

    public function __call($name, $arguments) {
        if( !preg_match('/^assert\S+/', $name) ) {
            return parent::__call($name, $arguments);
        }
        return $this->assert( call_user_func_array(['Mozy\Test\Assertion', $name], $arguments) );
    }

    protected function assert(Assertion $assertion) {
        if( $this->record )
            $this->assertions[] = $assertion;

        if( $assertion->result == false ) {
            throw new TestFailureException($assertion);
        }
        return $assertion;
    }

    public function setRecord($bool) {
        $this->record = (bool) $bool;
    }

    public function getPassedAssertions() {
        $array = [];
        array_walk($this->assertions, function($assertion, $key) use (&$array) {
            if($assertion->result == true) $array[] = $assertion;
        });
        return $array;
    }

    public function getFailedAssertions() {
        $array = [];
        array_walk($this->assertions, function($assertion, $key) use (&$array) {
            if($assertion->result == false) $array[] = $assertion;
        });
        return $array;
    }
}
?>