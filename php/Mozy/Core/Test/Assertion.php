<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\Object;
use Mozy\Core\Exception;
use Mozy\Core\Console;

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
        $condition = (string) Core\_S($condition) . ' is ' . 'TRUE';
        return Assertion::construct('AssertTrue', $condition, $result);
    }

    public static function assertException(Exception $exception = null, $type) {
        $result = (bool) ($exception instanceOf $type);
        $condition = (string) 'Expected exception ' . $type . ', received ' . ($exception ? $exception->name : 'none');
        return Assertion::construct('AssertException', $condition, $result);
    }

    public static function assertNoException(Exception $exception = null) {
        $result = (bool) (is_null($exception));
        $condition = (string) 'No exception expected, received ' . ($exception ? $exception->name : 'none');
        return Assertion::construct('AssertNoException', $condition, $result);
    }

    public static function assertEqual($obj1, $obj2) {
        $result = (bool) ($obj1 === $obj2);
        $condition = (string) Core\_S($obj1) . ' is equal to ' . Core\_S($obj2);
        return Assertion::construct('AssertEqual', $condition, $result);
    }

    public static function assertOutput($expectedOutput) {
        $output = ob_get_clean();
        $result = (bool) ($expectedOutput == $output);
        $condition = (string) "Expected output '" . $expectedOutput . "', received '" . $output ."'";
        return Assertion::construct('AssertOutput', $condition, $result);
    }

    public static function assertNoOutput() {
        $output = ob_get_clean();
        $result = (bool) !$output;
        $condition = (string) "No output expected, received '" . $output ."'";
        return Assertion::construct('AssertNoOutput', $condition, $result);
    }
}
?>