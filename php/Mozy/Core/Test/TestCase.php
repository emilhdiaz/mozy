<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\Object;
use Mozy\Core\Singleton;
use Mozy\Core\ApplicationContext;

abstract class TestCase extends Object implements Singleton, Testable {

    protected $name;
    protected $testScenario;
    protected $tests = [];
    protected $result = PENDING;
    protected $message;

    // test pre-conditions
    protected $requires;
    protected $dependsOn;
    protected $provider;
    private $variations;

    // test post-conditions
    protected $expectedException;
    protected $expectedOutput;

    protected function __construct(TestScenario $testScenario) {
        $this->name = $this->class->name;
        $this->testScenario = $testScenario;

        $comment = $this->testScenario->class->getMethod($this->shortName)->getComment();

        $this->requires          = Core\_A($comment->getAnnotation('requires'));
        $this->dependsOn         = Core\_A($comment->getAnnotation('dependsOn'));
        $this->provider          = $comment->getAnnotation('provider');
        $this->expectedException = $comment->getAnnotation('expectedException');
        $this->expectedOutput    = $comment->getAnnotation('expectedOutput');

        if( $this->provider ) {
            $this->variations = $testScenario->{$this->provider}() ?: [];
        } else {
            $this->variations[] = [];
        }
    }

    public function __toString() {
        return $this->name;
    }

    public function run() {
        global $framework;

        // check for runtime requirements
        $dependencyManager = $framework->getDependencyManager();
        foreach( $this->requires as $requirement=>$value ) {
            if( !$dependencyManager->isDependencyLoaded($requirement, $value) ) {
                $this->message = 'Runtime dependency on ' . $requirement . ' ' . $value . ' failed.';
                $this->result = SKIPPED;
                return;
            }
        }

        // check for dependencies
        foreach($this->dependsOn as $dependency) {
            $result = $this->testScenario->runTestCase($dependency);

            if( $result != PASSED ) {
                $this->message = 'Dependent test case ' . $dependency . ' did not pass.';
                $this->result = SKIPPED;
                return;
            }
        }

        // run all variations of the test case
        foreach($this->variations as $input) {
            $test = Test::construct($this, $input);
            $this->tests[] = $test;
            $test->run();

            // check if test case has failed
            if( ($test->result == FAILED) && ($this->testScenario->unitTest->stopOnFailure) ) {
                $this->message = 'Test variation ' . $test->name . ' failed.';
                $this->result = FAILED;
                return;
            }

            // check for incomplete tests
            if( $test->result == PENDING ) {
                // ignore the last run
                array_pop($this->tests);

                $this->message = 'No assertions defined.';
                $this->result = INCOMPLETE;
                return;
            }
        }

        // check results
        if( count($this->getFailed()) > 0 ) {
            $this->message = count($this->getFailed()) . ' test(s) failed.';
            $this->result = FAILED;
        }
        elseif( count($this->getPassed()) > 0 ) {
            $this->message = count($this->getPassed()) . ' test(s) passed.';
            $this->result = PASSED;
        }
        else {
            $this->message = 'No test variations defined.';
            $this->result = INCOMPLETE;
        }
    }

    public function getResult() {
        return $this->result;
    }

    public function getShortName() {
        return substr($this->name, strrpos($this->name, '_')+1);
    }

    public function getPrototype() {
        return $this->testScenario->class->getMethod($this->shortName)->getClosure($this->testScenario);
    }

    public function getPassed() {
        $array = [];
        array_walk($this->tests, function($test, $key) use (&$array) {
            if($test->result == PASSED) $array[] = $test;
        });
        return $array;
    }

    public function getFailed() {
        $array = [];
        array_walk($this->tests, function($test, $key) use (&$array) {
            if($test->result == FAILED) $array[] = $test;
        });
        return $array;
    }

    public function getPassedAssertions() {
        $array = [];
        array_walk($this->tests, function($test, $key) use (&$array) {
            $array += $test->getPassedAssertions();
        });
        return $array;
    }

    public function getFailedAssertions() {
        $array = [];
        array_walk($this->tests, function($test, $key) use (&$array) {
             $array += $test->getFailedAssertions();
        });
        return $array;
    }
}
?>