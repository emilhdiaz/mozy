<?php
namespace Mozy\APIs;

use Mozy\Core\API;
use Mozy\Core\Test\UnitTest;

class UnitTestAPI extends API {

    public function testAll( $stopOnFailure = false ) {
        global $framework;
        $unitTest = UnitTest::construct($stopOnFailure);
        $unitTest->discoverTests($framework->class->namespace->name);
        $unitTest->run();
        return $unitTest;
    }

    public function testNamespace( $namespace, $stopOnFailure = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        $unitTest->discoverTests($namespace);
        $unitTest->run();
        return $unitTest;
    }

    public function testNamespaces( array $namespaces, $stopOnFailure = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        foreach($namespaces as $namespace) {
            $unitTest->discoverTests($namespace);
        }
        $unitTest->run();
        return $unitTest;
    }

    public function testScenario( $scenario, $stopOnFailure = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        $testScenario = $scenario::construct($unitTest);
        $unitTest->addTestScenario($testScenario);
        $unitTest->run();
        return $unitTest;
    }

    public function testScenarios( array $scenarios, $stopOnFailure = false ) {
        $unitTest = UnitTest::construct($stopOnFailure);
        foreach($scenarios as $scenario) {
            $testScenario = $scenario::construct($unitTest);
            $unitTest->addTestScenario($testScenario);
        }
        $unitTest->run();
        return $unitTest;
    }
}
?>