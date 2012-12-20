<?php
namespace Mozy\Core\Test;

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

    protected $namespaces = [];
    protected $testScenarios = [];
    protected $result = PENDING;
    protected $message;

    // options
    protected $defaultNamespace;
    protected $stopOnFailure;

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

    protected function __construct($stopOnFailure = false) {
        $this->stopOnFailure = (bool) $stopOnFailure;
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
            $path = get_path_namespace($namespace);
            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::CURRENT_AS_FILEINFO | RecursiveDirectoryIterator::SKIP_DOTS);
            $directory = new RecursiveIteratorIterator($directory);


            foreach( $directory as $filePath ) {
                if ( !($testScenario = get_class_from_filename($filePath)) )
                	continue;

                // filter non Test files
                if ( !preg_match(self::TestScenarioNameRegex, $testScenario) )
                    continue;

                // filter non Test classes
                if ( !is_a($testScenario, 'Mozy\Core\Test\TestScenario', true) )
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
            $testScenario->run();

            // check if scenario has failed
            if ( ($testScenario->result == FAILED) && ($this->stopOnFailure) ) {
                $this->result = FAILED;
                return;
            }
        }

        if ( count($this->failed) > 0 ) {
            $this->message = count($this->failed) . ' test scenario(s) failed.';
            $this->result = FAILED;
        }
        elseif ( count($this->passed) > 0 ) {
            $this->message = count($this->passed) . ' test scenario(s) passed.';
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

    public function setStopOnFailure($flag) {
        return $this->stopOnFailure = (bool) $flag;
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

    public function getPassed() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if ($testScenario->result == PASSED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getFailed() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if ($testScenario->result == FAILED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getSkipped() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if ($testScenario->result == SKIPPED) $array[] = $testScenario;
        });
        return $array;
    }

    public function getIncomplete() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            if ($testScenario->result == INCOMPLETE) $array[] = $testScenario;
        });
        return $array;
    }

    public function getPassedAssertions() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
            $array += $testScenario->passedAssertions;
        });
        return $array;
    }

    public function getFailedAssertions() {
        $array = [];
        array_walk($this->testScenarios, function($testScenario, $key) use (&$array) {
             $array += $testScenario->failedAssertions;
        });
        return $array;
    }
}
?>