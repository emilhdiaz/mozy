<?php
namespace Mozy\Core;

use Mozy\Core\Test\TestScenario;
use Mozy\Core\Test\TestException;

class ExceptionsTest extends TestScenario {

    protected $separateProcesses = false;

    /**
     * @expectedException Mozy\Core\ResourceNotFoundError
     */
    public function testResourceNotFoundError() {
        RandomMissingClass::construct();
    }

    /**
     * @expectedException Mozy\Core\ClassNotFoundError
     */
    public function testClassNotFoundError() {
    	MissingClass::construct();
    }

    /**
     * @expectedException Mozy\Core\InterfaceNotFoundError
     */
    public function testInterfaceNotFoundError() {
    	MissingInterface::construct();
    }

    /**
     * @expectedException Mozy\Core\TraitNotFoundError
     */
    public function testTraitNotFoundError() {
    	MissingTrait::construct();
    }

    public function testFileNotFoundError() {

    }

    /**
     * @expectedException Mozy\Core\UndefinedMethodError
     */
    public function testUndefinedMethodError() {
    	$this->undefinedMethod();
    }

    /**
     * @expectedException Mozy\Core\UndefinedPropertyError
     */
     public function testUndefinedPropertyError() {
     	$this->undefinedProperty;
     }
}
?>
