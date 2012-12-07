<?php
namespace Mozy\APIs;

use Mozy\Core\API;
use Mozy\Core\Test\UnitTest;

class UnitTestAPI extends API {

    private function runUnitTest( $unitTest, $stopOnFailure = false, $reportMode = false ) {
        $report = $unitTest->run();

        if($reportMode) {
            $report->setMode($reportMode);
        }

        return $report;
    }

    public function testAll( $stopOnFailure = false, $separateProcess = false, $reportMode = false ) {
        global $framework;
        $unitTest = UnitTest::construct($stopOnFailure, $separateProcess);
        $unitTest->discoverTests($framework->class->namespace->name);
        $report = $this->runUnitTest($unitTest, $stopOnFailure, $reportMode);
        return $report;
    }

    public function testNamespace( $namespace, $stopOnFailure = false, $separateProcess = false, $reportMode = false ) {
        $unitTest = UnitTest::construct($stopOnFailure, $separateProcess);
        $unitTest->discoverTests($namespace);
        return $this->runUnitTest($unitTest, $stopOnFailure, $reportMode);
    }

    public function testNamespaces( array $namespaces, $stopOnFailure = false, $separateProcess = false, $reportMode = false ) {
        $unitTest = UnitTest::construct($stopOnFailure, $separateProcess);
        foreach($namespaces as $namespace) {
            $unitTest->discoverTests($namespace);
        }
        return $this->runUnitTest($unitTest, $stopOnFailure, $reportMode);
    }

    public function testScenario( $scenario, $stopOnFailure = false, $separateProcess = false, $reportMode = false ) {
        $unitTest = UnitTest::construct($stopOnFailure, $separateProcess);
        $testScenario = $scenario::construct($unitTest);
        $unitTest->addTestScenario($testScenario);
        return $this->runUnitTest($unitTest, $stopOnFailure, $reportMode);
    }

    public function testScenarios( array $scenarios, $stopOnFailure = false, $separateProcess = false, $reportMode = false ) {
        $unitTest = UnitTest::construct($stopOnFailure, $separateProcess);
        foreach($scenarios as $scenario) {
            $testScenario = $scenario::construct($unitTest);
            $unitTest->addTestScenario($testScenario);
        }
        return $this->runUnitTest($unitTest, $stopOnFailure, $reportMode);
    }
}
?>