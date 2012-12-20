<?php
namespace Mozy\Core\Test;

use Mozy\Core\Object;
use Mozy\Core\Exception;

class Assertion extends Object {

    protected $type;
    protected $condition;
    protected $result;

    protected function __construct($type, $condition, $result) {
        $this->type = $type;
        $this->condition = $condition;
        $this->result = $result;
    }

    public static function failed($type, $condition) {
        return Assertion::construct($type, $condition, false);
    }

    public static function assertTrue($condition) {
        $result = (bool) assert($condition);
        $condition = _S($condition) . ' is true';
        return Assertion::construct('AssertTrue', $condition, $result);
    }

    public static function assertException(Exception $exception = null, $type) {
        $result = (bool) ($exception instanceOf $type);
        $condition = 'Expected exception ' . $type . ', received ' . ($exception ? $exception->name : 'none');
        return Assertion::construct('AssertException', $condition, $result);
    }

    public static function assertNoException(Exception $exception = null) {
        $result = (bool) (is_null($exception));
        $condition = 'No exception expected, received ' . ($exception ? $exception->name : 'none');
        return Assertion::construct('AssertNoException', $condition, $result);
    }

    public static function assertEqual($obj1, $obj2) {
        $result = (bool) ($obj1 === $obj2);
        $condition = _S($obj1) . ' is equal to ' . _S($obj2);
        return Assertion::construct('AssertEqual', $condition, $result);
    }

    public static function assertOutput($expectedOutput) {
    	global $process;
        $output = $process->out->contents;
        $result = (bool) ($expectedOutput == $output);
        $condition = "Expected output '" . $expectedOutput . "', received '" . $output ."'";
        return Assertion::construct('AssertOutput', $condition, $result);
    }

    public static function assertNoOutput() {
        global $process;
        $output = $process->out->contents;
        $result = (bool) !$output;
        $condition = "No output expected, received '" . $output ."'";
        return Assertion::construct('AssertNoOutput', $condition, $result);
    }

    public static function assertIsClass( Object $object, $className ) {
        $objectClass = get_class($object);
        $result = (bool) ($objectClass == $className);
        $condition = 'Expected object of class ' . $className . ', received object of class ' . $objectClass;
        return Assertion::construct('AssertIsClass', $condition, $result);
    }
}
?>