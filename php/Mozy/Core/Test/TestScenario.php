<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\Object;
use Mozy\Core\Singleton;
use Mozy\Core\ApplicationContext;
use Mozy\Core\Factory;
use Mozy\Core\Reflection\ReflectionMethod;

abstract class TestScenario extends Object implements Singleton, Testable {
    use Assertive;

    const TestCaseNameRegex   = '/test.*/';

    protected $name;
    protected $unitTest;
    protected $testCases = [];
    protected $result = PENDING;
    protected $message;

    // test pre-conditions
    protected $fixture;
    protected $requires;


    protected function __construct(UnitTest $unitTest) {
        global $framework;

        $this->name = $this->class->name;
        $this->unitTest = $unitTest;
        $this->requires = Core\_A($this->class->getComment()->getAnnotation('requires'));

        $class = $framework->factory->reflect(Core\TestCase);
        foreach( $this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $test ) {
            // filter non test methods
            if( !preg_match(self::TestCaseNameRegex, $test->name) )
                continue;

            // filter static methods
            if( $test->isStatic() )
                continue;

            // create a new TestCase subclass
            $testCaseClass = $framework->factory->extend($class, $this->class->name . '_' . $test->name);

            $testCase = $testCaseClass::construct($this);

            $this->testCases[$testCase->shortName] = $testCase;
        }
    }

    public function __toString() {
        return $this->name;
    }

    public function setUp() {
        $this->fixture = new \StdClass;
        return $this;
    }

    public function tearDown() {
        $this->fixture = null;
        return $this;
    }

    public function run() {

        // check for runtime requirements
        $dependencyManager = $this->getFramework()->getDependencyManager();
        foreach( $this->requires as $requirement=>$value ) {
            if( !$dependencyManager->isDependencyLoaded($requirement, $value) ) {
                $this->message = 'Runtime dependency on ' . $requirement . ' ' . $value . ' failed.';
                $this->result = SKIPPED;
                return;
            }
        }

        foreach($this->testCases as $testCase) {
            // check for already executed tests
            if( $testCase->result != PENDING )
                continue;

            $this->runTestCase($testCase->shortName);

            // check if scenario has failed
            if( ($testCase->result == FAILED) && ($this->unitTest->stopOnFailure) ) {
                $this->message = 'Test case ' . $testCase->name . ' failed.';
                $this->result = FAILED;
                return;
            }
        }

        if( count($this->getFailed()) > 0 ) {
            $this->message = count($this->getFailed()) . ' test case(s) failed.';
            $this->result = FAILED;
        }
        elseif( count($this->getPassed()) > 0 ) {
            $this->message = count($this->getPassed()) . ' test case(s) passed.';
            $this->result = PASSED;
        }
        else {
            $this->message = 'No test cases ran.';
            $this->result = INCOMPLETE;
        }
    }

    public function runTestCase($name) {
        #TODO throw exception is not found

        $testCase = $this->testCases[$name];

        // check if test already ran
        if( $testCase->result != PENDING )
            return $testCase->result;

        // set up before test
        $this->setUp();

        // run test case
        $testCase->run();

        // tear down after test
        $this->tearDown();

        return $testCase->result;
    }

    public function getResult() {
        return $this->result;
    }

    public function getPassed() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
            if($testCase->result == PASSED) $array[] = $testCase;
        });
        return $array;
    }

    public function getFailed() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
            if($testCase->result == FAILED) $array[] = $testCase;
        });
        return $array;
    }

    public function getSkipped() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
            if($testCase->result == SKIPPED) $array[] = $testCase;
        });
        return $array;
    }

    public function getIncomplete() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
            if($testCase->result == INCOMPLETE) $array[] = $testCase;
        });
        return $array;
    }

    public function getPassedAssertions() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
            $array += $testCase->getPassedAssertions();
        });
        return $array;
    }

    public function getFailedAssertions() {
        $array = [];
        array_walk($this->testCases, function($testCase, $key) use (&$array) {
             $array += $testCase->getFailedAssertions();
        });
        return $array;
    }
}
?>