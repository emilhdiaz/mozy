<?php
namespace Mozy\Core\Test;

use Mozy\Core\Test\TestScenario;
use Mozy\Core\Test\TestException;

/**
 * @requires PHP 5.4.9
 * @ignore
 */
class B_SampleTest extends TestScenario {

    public $object;

    public function setup() {
        parent::setup();
        $this->fixture->object = new \StdClass;
    }

    public function cleanup() {
        parent::cleanup();
        unset($this->fixture->object);
    }

    public function testIncomplete() {

    }

    public function testFailure() {
        $this->assertTrue(false);
    }

    public function providePassword() {
        return [
            123,
            456
        ];
    }

    /**
     * @expectedException Mozy\Core\Test\TestException
     */
    public function testObjectCreation() {
        throw new TestException($this);
    }

    /**
     * This is a description
     * and a second line
     *
     * @dependsOn testObjectCreation
     * @provider providePassword
     */
    public function testSomething($password) {
        # lookup user password
        $pass = 123;

        $this->assertEqual($password, $pass);
#        $this->assertEqual($this, $this);
    }
}
?>
