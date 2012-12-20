<?php
namespace Mozy\Core;

use Mozy\Core\Test\TestScenario;
use Mozy\Core\Test\TestException;

ini_set('display_errors', true);

class ExceptionsTest extends TestScenario {

    protected $separateProcesses = true;

    /**
     * @expectedException Mozy\Core\FileNotFoundError
     */
    public function testClassNotFoundError() {
        $obj = RandomMissingClass::construct();
    }
}
?>
