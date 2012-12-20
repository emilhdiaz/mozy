<?php
namespace Mozy\APIs;

use Mozy\Core\Test;
use Mozy\Core\Test\UnitTest;
use Mozy\Core\System\Console\Console;
use Mozy\Core\System\Console\ConsoleOutput;
use Mozy\Core\System\Console\ConsoleView;

class UnitTestConsoleView extends ConsoleView {

    protected $unitTest;
    protected $mode = 'all';

    protected function __construct( Console $console ) {
        $this->console = $console;
    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function testAll( UnitTest $unitTest ) {
    	$this->printResults($unitTest);
    }

    public function testNamespace( UnitTest $unitTest ) {
    	$this->printResults($unitTest);
    }

    public function testNamespaces( UnitTest $unitTest ) {
    	$this->printResults($unitTest);
    }

    public function testScenario( UnitTest $unitTest ) {
    	$this->printResults($unitTest);
    }

    public function testScenarios( UnitTest $unitTest ) {
    	$this->printResults($unitTest);
    }

    public function printResults( UnitTest $unitTest ) {
		global $process;

		$process->out->buffer()->clean();

    	$output = $this->console->output;

        $output = ConsoleOutput::construct();

        $output->override(Test\PASSED, 'bold', 'green');
        $output->override(Test\FAILED, 'bold', 'red');
        $output->override(Test\PENDING, 'bold', 'yellow');
        $output->override(Test\SKIPPED, 'bold', 'green');
        $output->override(Test\INCOMPLETE, 'bold', 'magenta');

        #TODO: add something indicating what options are used and what namespaces were tested

        // check for skipped test scenarios and test cases
        $skippedTestScenarios = $unitTest->skipped;
        $skippedTestCases = $this->skippedTestCases($unitTest);

        // check for incomplete test scenarios and test cases
        $incompleteTestScenarios = $unitTest->incomplete;
        $incompleteTestCases = $this->incompleteTestCases($unitTest);

        if ( $skippedTestScenarios + $skippedTestCases + $incompleteTestScenarios + $incompleteTestCases )
            $output->line('Warnings:', 0, 'yellow');

        if ( count($skippedTestScenarios) > 0) {
            $output->line(' The following Test Scenarios were skipped: ', null, 'yellow');
            $output->each($skippedTestScenarios, '  -');
        }

        if ( count($skippedTestCases) > 0) {
            $output->line(' The following Test Cases were skipped: ', null, 'yellow');
            $output->each($skippedTestCases, '  -');
        }

        if ( count($incompleteTestScenarios) > 0) {
            $output->line(' The following Test Scenarios were incomplete: ', null, 'yellow');
            $output->each($incompleteTestScenarios, '  -');
        }

        if ( count($incompleteTestCases) > 0) {
            $output->line(' The following Test Cases were incomplete: ', null, 'yellow');
            $output->each($incompleteTestCases, '  -');
        }

        // TEST SCENARIOS
        foreach($unitTest->testScenarios as $testScenario) {
            if ( $testScenario->result == Test\PENDING )
                continue;

            $passed = count($testScenario->passed);
            $failed = count($testScenario->failed);
            $skipped = count($testScenario->skipped);
            $incomplete = count($testScenario->incomplete);
            $passedAssertions = count($testScenario->passedAssertions);
            $failedAssertions = count($testScenario->failedAssertions);

            $total = $passed + $failed;
            $totalAssertions = $passedAssertions + $failedAssertions;

            $output->line('__________________________________________________________________________________________________', 'bold', 'white');
            $output->text(' Test Scenario ' . $testScenario->name, 'bold', 'cyan');
            $output->text(' - ' . $testScenario->result);
            $output->nl();

            if ( $testScenario->result == Test\PASSED || $testScenario->result == Test\FAILED ) {
                $output->line(
                    ' Test Cases('. $total .': '. $passed .' PASSED / '. $failed .' FAILED / '. $skipped .' SKIPPED / '. $incomplete .' INCOMPLETE), '
                    .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)',
                    'bold', 'black', null, true
                );
            }
            else {
               $output->line(' ' . $testScenario->message, 'bold', 'black');
            }

            // check reporting mode
            if ( ($testScenario->result == Test\PASSED) && ($this->mode != 'all') )
                continue;

            // TEST CASES
            foreach($testScenario->testCases as $testCase) {
                if ( $testCase->result == Test\PENDING )
                    continue;

                // check reporting mode
                if ( ($testCase->result == Test\PASSED) && ($this->mode != 'all') )
                    continue;

                $passed = count($testCase->passed);
                $failed = count($testCase->failed);
                $passedAssertions = count($testCase->passedAssertions);
                $failedAssertions = count($testCase->failedAssertions);

                $total = $passed + $failed;
                $totalAssertions = $passedAssertions + $failedAssertions;

                $output->nl();
                $output->text(' + Test Case #' . $testCase->shortName, 'bold', 'blue');
                $output->text(' - '. $testCase->result);
                $output->nl();

                if ( $testCase->result == Test\PASSED || $testCase->result == Test\FAILED ) {
                    $output->line(
                        '   Tests('. $total .': '. $passed .' PASSED / '. $failed .' FAILED), '
                        .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)',
                        'bold', 'black', null, true
                    );
                }
                else {
                    $output->line('   ' . $testCase->message, 'bold', 'black');
                }

                // TESTS
                foreach($testCase->tests as $test) {
                    // check reporting mode
                    if ( ($test->result == Test\PASSED) && ($this->mode != 'all') )
                        continue;

                    $output->text('    -> ');
                    $output->text($test->name, null, 'cyan');
                    $output->text(' - ' . $test->result . ' - ');
                    $output->text(
                        (
                            $test->failure ?
                            $test->failure->message :
                                'Assertions(' . count($test->passed)
                        ) . ')', 'bold', 'black', null, true
                    );
                    $output->nl();
                }
            }
        }

        $output->nl();
        $output->line('Test Complete.');
		$process->out->flush()->end();
	}

    private function getPassedTestCases( UnitTest $unitTest ) {
        $array = [];
        foreach($unitTest->testScenarios as $testScenario) {
            $array += $testScenario->passed;
        }
        return $array;
    }

    private function getFailedTestCases( UnitTest $unitTest ) {
        $array = [];
        foreach($unitTest->testScenarios as $testScenario) {
             $array += $testScenario->failed;
        }
        return $array;
    }

    private function getSkippedTestCases( UnitTest $unitTest ) {
        $array = [];
        foreach($unitTest->testScenarios as $testScenario) {
            $array += $testScenario->skipped;
        }
        return $array;
    }

    private function getIncompleteTestCases( UnitTest $unitTest ) {
        $array = [];
        foreach($unitTest->testScenarios as $testScenario) {
             $array += $testScenario->incomplete;
        }
        return $array;
    }
}
?>
