<?php
namespace Mozy\Core;

use Mozy\Test\TestScenario;
use Mozy\Test\TestException;

ini_set('display_errors', true);

class ExceptionsTest extends TestScenario {

    protected $separateProcesses = true;

    /**
     * @expectedException Mozy\Core\ClassNotFoundError
     */
    public function testClassNotFoundError() {
        $obj = RandomMissingClass::construct();
    }
}
?>