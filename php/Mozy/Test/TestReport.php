<?php
namespace Mozy\Test;

use Mozy\Core\Object;
use Mozy\Core\System\ConsoleOutput;

class TestReport extends Object {
    protected $unitTest;
    protected $mode;

    protected function __construct(UnitTest $unitTest) {
        $this->unitTest = $unitTest;
    }

    public function setMode($mode) {
        $this->mode = $mode;
    }

    public function getPassedTestCases() {
        $array = [];
        foreach($this->unitTest->testScenarios as $testScenario) {
            $array += $testScenario->passed;
        }
        return $array;
    }

    public function getFailedTestCases() {
        $array = [];
        foreach($this->unitTest->testScenarios as $testScenario) {
             $array += $testScenario->failed;
        }
        return $array;
    }

    public function getSkippedTestCases() {
        $array = [];
        foreach($this->unitTest->testScenarios as $testScenario) {
            $array += $testScenario->skipped;
        }
        return $array;
    }

    public function getIncompleteTestCases() {
        $array = [];
        foreach($this->unitTest->testScenarios as $testScenario) {
             $array += $testScenario->incomplete;
        }
        return $array;
    }

    public function __revive(TestReport $report) {
        // nothing of interest
    }

    public function __toText() {
        global $framework;

        $unitTest = $this->unitTest;

        $output = ConsoleOutput::construct();

        $output->override(PASSED, 'bold', 'green');
        $output->override(FAILED, 'bold', 'red');
        $output->override(PENDING, 'bold', 'yellow');
        $output->override(SKIPPED, 'bold', 'green');
        $output->override(INCOMPLETE, 'bold', 'magenta');

        $output->nl();
        $output->line('##################################################################################################', 'bold', 'black');
        $output->line(' Mozy Framework ' . $framework->version . ' - Unit Testing', 'bold', 'white');
        $output->line(' (c) Copywrite of Mozy Framework. All rights reserved.', 'bold', 'white');
        $output->line('##################################################################################################', 'bold', 'black');
        $output->nl();

        #TODO: add something indicating what options are used and what namespaces were tested

        // check for skipped test scenarios and test cases
        $skippedTestScenarios = $unitTest->skipped;
        $skippedTestCases = $this->skippedTestCases;

        // check for incomplete test scenarios and test cases
        $incompleteTestScenarios = $unitTest->incomplete;
        $incompleteTestCases = $this->incompleteTestCases;

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
            if ( $testScenario->result == PENDING )
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

            if ( $testScenario->result == PASSED || $testScenario->result == FAILED ) {
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
            if ( ($testScenario->result == PASSED) && ($this->mode != 'all') )
                continue;

            // TEST CASES
            foreach($testScenario->testCases as $testCase) {
                if ( $testCase->result == PENDING )
                    continue;

                // check reporting mode
                if ( ($testCase->result == PASSED) && ($this->mode != 'all') )
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

                if ( $testCase->result == PASSED || $testCase->result == FAILED ) {
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
                    if ( ($test->result == PASSED) && ($this->mode != 'all') )
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
        $output->nl();
        return $output;
    }

    public function __toSerial() {
        return serialize($this);
    }
}
?>
