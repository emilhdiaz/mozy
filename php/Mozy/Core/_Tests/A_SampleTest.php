<?php
namespace Mozy\Core;

use Mozy\Test\TestScenario;
use Mozy\Test\TestException;

/**
 * @requires PHP 5.4.9
 */
class A_SampleTest extends TestScenario {

    public $object;

    public function setUp() {
        parent::setUp();
        $this->fixture->object = new \StdClass;
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->fixture->object);
    }

    public function providePassword() {
        return [
            123,
            123,
            456,
            true,
            123,
            false
        ];
    }

    /**
     * This is a description
     * and a second line
     *
     * @provider providePassword
     */
    public function testSomethingElse($password) {
        # lookup user password
        $pass = 123;

        $this->assertEqual($password, $pass);
#        $this->assertEqual($this, $this);
    }
}
?>
