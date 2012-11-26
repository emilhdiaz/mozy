<?php
namespace Mozy\APIs;

use Mozy\Core\API;
use Mozy\Core\ApplicationContext;
use Mozy\Core\Test\UnitTest;

class UnitTestAPI extends API {

    private function runUnitTest( $unitTest, $stopOnFailure = false, $report = false ) {
        $unitTest->run();

        if($report) {
            $reportAll = (bool) ($report === 'all');
            $unitTest->getReport($reportAll)->__toText();
        }
    }

    public function testAll( $stopOnFailure = false, $report = false ) {
        global $framework;
        $unitTest = UnitTest::construct($stopOnFailure);
        $unitTest->discoverTests($framework->class->getNamespace()->name);
        $this->runUnitTest($unitTest, $stopOnFailure, $report);
    }

    public function testNamespace( $namespace, $stopOnFailure = false, $report = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        $unitTest->discoverTests($namespace);
        $this->runUnitTest($unitTest, $stopOnFailure, $report);
    }

    public function testNamespaces( array $namespaces, $stopOnFailure = false, $report = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        foreach($namespaces as $namespace) {
            $unitTest->discoverTests($namespace);
        }
        $this->runUnitTest($unitTest, $stopOnFailure, $report);
    }

    public function testScenario( $scenario, $stopOnFailure = false, $report = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        $testScenario = $scenario::construct($unitTest);
        $unitTest->addTestScenario($testScenario);
        $this->runUnitTest($unitTest, $stopOnFailure, $report);
    }

    public function testScenarios( array $scenarios, $stopOnFailure = false, $report = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        foreach($scenarios as $scenario) {
            $testScenario = $scenario::construct($unitTest);
            $unitTest->addTestScenario($testScenario);
        }
        $this->runUnitTest($unitTest, $stopOnFailure, $report);
    }
}
?>