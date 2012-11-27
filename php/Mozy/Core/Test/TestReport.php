<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\ApplicationContext;
use Mozy\Core\Object;

class TestReport extends Object {

    protected $unitTest;
    protected $reportAll;

    protected function __construct(UnitTest $unitTest, $reportAll = false) {
        $this->unitTest = $unitTest;
        $this->reportAll = $reportAll;
    }

    public function __toText() {
        global $framework;

        $unitTest = $this->unitTest;

        $output = $framework->console->output;

        $output->overrides(PASSED, 'bold', 'green');
        $output->overrides(FAILED, 'bold', 'red');
        $output->overrides(PENDING, 'bold', 'yellow');
        $output->overrides(SKIPPED, 'bold', 'green');
        $output->overrides(INCOMPLETE, 'bold', 'magenta');

        $output->nl();
        $output->line('##################################################################################################', 'bold', 'black');
        $output->line(' Mozy Framework ' . $framework->version . ' - Unit Testing', 'bold', 'white');
        $output->line(' (c) Copywrite of Mozy Framework. All rights reserved.', 'bold', 'white');
        $output->line('##################################################################################################', 'bold', 'black');
        $output->nl();

        #TODO: add something indicating what options are used and what namespaces were tested

        // check for skipped test scenarios and test cases
        $skippedTestScenarios = $unitTest->getSkipped();
        $skippedTestCases = $unitTest->getSkippedTestCases();


        // check for incomplete test scenarios and test cases
        $incompleteTestScenarios = $unitTest->getIncomplete();
        $incompleteTestCases = $unitTest->getIncompleteTestCases();

        if( $skippedTestScenarios + $skippedTestCases + $incompleteTestScenarios + $incompleteTestCases )
            $output->line('Warnings:', 0, 'yellow');

        if( count($skippedTestScenarios) > 0) {
            $output->line(' The following Test Scenarios were skipped: ', null, 'yellow');
            $output->each($skippedTestScenarios, '  -');
        }

        if( count($skippedTestCases) > 0) {
            $output->line(' The following Test Cases were skipped: ', null, 'yellow');
            $output->each($skippedTestCases, '  -');
        }

        if( count($incompleteTestScenarios) > 0) {
            $output->line(' The following Test Scenarios were incomplete: ', null, 'yellow');
            $output->each($incompleteTestScenarios, '  -');
        }

        if( count($incompleteTestCases) > 0) {
            $output->line(' The following Test Cases were incomplete: ', null, 'yellow');
            $output->each($incompleteTestCases, '  -');
        }

        // TEST SCENARIOS
        foreach($unitTest->testScenarios as $testScenario) {
            if( $testScenario->result == PENDING )
                continue;

            $passed = count($testScenario->getPassed());
            $failed = count($testScenario->getFailed());
            $skipped = count($testScenario->getSkipped());
            $incomplete = count($testScenario->getIncomplete());
            $passedAssertions = count($testScenario->getPassedAssertions());
            $failedAssertions = count($testScenario->getFailedAssertions());

            $total = $passed + $failed;
            $totalAssertions = $passedAssertions + $failedAssertions;

            $output->line('__________________________________________________________________________________________________');
            $output->text(' Test Scenario ' . $testScenario->name, 'bold', 'cyan');
            $output->text(' - ' . $testScenario->result);
            $output->nl();

            if( $testScenario->result == PASSED || $testScenario->result == FAILED ) {
                $output->line(
                    ' Test Cases('. $total .': '. $passed .' PASSED / '. $failed .' FAILED / '. $skipped .' SKIPPED / '. $incomplete .' INCOMPLETE), '
                    .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)',
                    'bold', 'black'
                );
            }
            else {
               $output->line(' ' . $testScenario->message, 'bold', 'black');
            }

            // check reporting mode
            if( ($testScenario->result == PASSED) && ($this->reportAll == false) )
                return;

            // TEST CASES
            foreach($testScenario->testCases as $testCase) {
                if( $testCase->result == PENDING )
                    continue;

                // check reporting mode
                if( ($testCase->result == PASSED) && ($this->reportAll == false) )
                    return;

                $passed = count($testCase->getPassed());
                $failed = count($testCase->getFailed());
                $passedAssertions = count($testCase->getPassedAssertions());
                $failedAssertions = count($testCase->getFailedAssertions());

                $total = $passed + $failed;
                $totalAssertions = $passedAssertions + $failedAssertions;

                $output->nl();
                $output->text(' + Test Case #' . $testCase->shortName, 'bold', 'blue');
                $output->text(' - '. $testCase->result);
                $output->nl();

                if( $testCase->result == PASSED || $testCase->result == FAILED ) {
                    $output->line(
                        '   Tests('. $total .': '. $passed .' PASSED / '. $failed .' FAILED), '
                        .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)',
                        'bold', 'black'
                    );
                }
                else {
                    $output->line('   ' . $testCase->message, 'bold', 'black');
                }

                // TESTS
                foreach($testCase->tests as $test) {
                    // check reporting mode
                    if( ($test->result == PASSED) && ($this->reportAll == false) )
                        return;

                    $output->text('    -> ');
                    $output->text($test->name, null, 'cyan');
                    $output->text(' - ' . $test->result . ' - ');
                    $output->text(
                        (
                            $test->failure ?
                            $test->failure->getMessage() :
                                'Assertions(' . count($test->getPassed())
                        ) . ')', 'bold', 'black'
                    );
                    $output->nl();
                }
            }
        }

        $output->nl();
        $output->line('Test Complete.');
        $output->nl();
        $output->send();
    }
}
?>
