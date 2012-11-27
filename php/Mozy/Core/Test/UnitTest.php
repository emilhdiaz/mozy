<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\Object;
use Mozy\Core\Singleton;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

const PENDING   = 'PENDING';
const PASSED    = 'PASSED';
const FAILED    = 'FAILED';
const SKIPPED   = 'SKIPPED';
const IGNORED   = 'IGNORED';
const INCOMPLETE= 'INCOMPLETE';

class UnitTest extends Object implements Singleton, Testable {

    const TestScenarioNameRegex     = '/(\S*)(\w+Test)$/';

    protected $testScenarios = [];
    protected $namespaces = [];
    protected $result = PENDING;
    protected $message;

    // options
    protected $defaultNamespace;
    protected $stopOnFailure;
    protected $separateProcess;

    protected $filterGroups;            // scenario
    protected $filterGroupsPattern;     // scenario
    protected $excludedGroups;          // scenario
    protected $excludedGroupsPattern;   // scenario

    protected $filterSuites;            // tester
    protected $filterSuitesPattern;     // tester
    protected $filterScenarios;         // tester
    protected $filterScenariosPattern;  // tester

    protected $excludedSuites;          // tester
    protected $excludedSuitesPattern;   // tester
    protected $excludedScenarios;       // tester
    protected $excludedScenariosPattern;// tester

    protected function __construct($stopOnFailure = false, $separateProcess = false) {
        $this->stopOnFailure = (bool) $stopOnFailure;
        $this->separateProcess = (bool) $separateProcess;
    }

    public function __toString() {
        return $this->name;
    }

    public function addTestScenario(TestScenario $testScenario) {
        $this->testScenarios[$testScenario->class->name] = $testScenario;

        return $this;
    }

    public function addTestScenarios(array $testScenarios) {
        foreach($testScenarios as $testScenario) {
            $this->addTestScenario($testScenario);
        }

        return $this;
    }

    public function discoverTests($namespace) {
        try{
            $path = Core\get_path_from_namespace($namespace);
            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::CURRENT_AS_FILEINFO | RecursiveDirectoryIterator::SKIP_DOTS);
            $directory = new RecursiveIteratorIterator($directory);


            foreach( $directory as $filePath ) {
                $testScenario = Core\get_class_from_filename($filePath);

                // filter non Test files
                if( !preg_match(self::TestScenarioNameRegex, $testScenario) )
                    continue;

                // filter non Test classes
                if( !is_a($testScenario, Core\TestScenario, true) )
                    continue;

                $this->addTestScenario( $testScenario::construct($this) );
            }
        } catch (\UnexpectedValueException $e) {
            throw $e;
        }
        $this->namespaces[] = $namespace;
        return $this;
    }

    public function run() {
        global $framework;

        foreach($this->testScenarios as $testScenario) {

            // run test scenario
            if( $this->separateProcess ) {
                $testScenario = $framework->executeTarget('UnitTest', 'testScenario', ['scenario'=>$testScenario->name, 'report'=>'all'], 'native', true);
            }

            else
                $testScenario->run();

            // check if scenario has failed
            if( ($testScenario->result == FAILED) && ($this->stopOnFailure) ) {
                $this->result = FAILED;
                return;
            }
        }

        if( count($this->getFailed()) > 0 ) {
            $this->message = count($this->getFailed()) . ' test scenario(s) failed.';
            $this->result = FAILED;
        }
        elseif( count($this->getPassed()) > 0 ) {
            $this->message = count($this->getPassed()) . ' test scenario(s) passed.';
            $this->result = PASSED;
        }
        else {
            $this->message = 'No test scenarios ran.';
            $this->result = INCOMPLETE;
        }
    }

    public function setDefaultNamespace($namespace) {
        return $this->defaultNamespace = $namespace;
    }

    public function setStopOnFailure($bool) {
        return $this->stopOnFailure = (bool) $stopOnFailure;
    }

    public function setFilterGroups(array $groups) {
        return $this->filterGroups = $groups;
    }

    public function setFilterSuites(array $suites) {
        return $this->filterSuites = $suites;
    }

    public function setFilterScenarios(array $scenarios) {
        return $this->filterScenarios = $scenarios;
    }

    public function setExcludedGroups(array $groups) {
        return $this->excludedGroups = $groups;
    }

    public function setExcludedSuites(array $suites) {
        return $this->excludedSuites = $suites;
    }

    public function setExcludedScenarios(array $scenarios) {
        return $this->excludedScenarios = $scenarios;
    }

    public function getResult() {
        return $this->result;
    }

    public function getReport($reportAll = false) {
        return TestReport::construct($this, $reportAll);
    }

    public function getPassed() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if($testScenario->result == PASSED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getFailed() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if($testScenario->result == FAILED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getSkipped() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if($testScenario->result == SKIPPED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getIncomplete() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if($testScenario->result == INCOMPLETE) $array[] = $testScenario;
        });
        return $array;
    }

    public function getPassedTestCases() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            $array += $testScenario->getPassed();
        });
        return $array;
    }

    public function getFailedTestCases() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
             $array += $testScenario->getFailed();
        });
        return $array;
    }

    public function getSkippedTestCases() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            $array += $testScenario->getSkipped();
        });
        return $array;
    }

    public function getIncompleteTestCases() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
             $array += $testScenario->getIncomplete();
        });
        return $array;
    }

    public function getPassedAssertions() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            $array += $testScenario->getPassedAssertions();
        });
        return $array;
    }

    public function getFailedAssertions() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
             $array += $testScenario->getFailedAssertions();
        });
        return $array;
    }
}
?>