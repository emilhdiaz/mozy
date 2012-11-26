<?php
namespace Mozy\Core\Test;

use Mozy\Core;
use Mozy\Core\Console;
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

        Console::println(' ');
        Console::println( Console::inColor('dark_gray', '##################################################################################################'));
        Console::println( Console::inColor('white', ' Mozy Framework ' . $framework->version . ' - Unit Testing' ) );
        Console::println( Console::inColor('white', ' (c) Copywrite of Mozy Framework. All rights reserved.') );
        Console::println( Console::inColor('dark_gray', '##################################################################################################'));
        Console::println(' ');

        #TODO: add something indicating what options are used and what namespaces were tested

        // check for skipped test scenarios and test cases
        $skippedTestScenarios = $unitTest->getSkipped();
        $skippedTestCases = $unitTest->getSkippedTestCases();


        // check for incomplete test scenarios and test cases
        $incompleteTestScenarios = $unitTest->getIncomplete();
        $incompleteTestCases = $unitTest->getIncompleteTestCases();

        if( $skippedTestScenarios + $skippedTestCases + $incompleteTestScenarios + $incompleteTestCases )
            Console::println( Console::inColor('yellow', 'Warnings:') );

        if( count($skippedTestScenarios) > 0) {
            Console::println( ' The following Test Scenarios were skipped: ');
            Console::printByLine($skippedTestScenarios);
        }

        if( count($skippedTestCases) > 0) {
            Console::println( ' The following Test Cases were skipped: ');
            Console::printByLine($skippedTestCases);
        }

        if( count($incompleteTestScenarios) > 0) {
            Console::println( ' The following Test Scenarios were incomplete: ');
            Console::printByLine($incompleteTestScenarios);
        }

        if( count($incompleteTestCases) > 0) {
            Console::println( ' The following Test Cases were incomplete: ');
            Console::printByLine($incompleteTestCases, '  -');
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

            Console::println('__________________________________________________________________________________________________');
            Console::println( Console::inColor('bold_cyan', ' Test Scenario ' . $testScenario->name) . ' - ' . colorResult($testScenario->result) );

            if( $testScenario->result == PASSED || $testScenario->result == FAILED ) {
                Console::println(
                    Console::inColor('dark_gray',
                    ' Test Cases('. $total .': '. $passed .' PASSED / '. $failed .' FAILED / '. $skipped .' SKIPPED / '. $incomplete .' INCOMPLETE), '
                    .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)'
                    )
                );
            }
            else {
                Console::println(
                    ' ' . Console::inColor('dark_gray',  $testScenario->message)
                );
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

                Console::println(' ');
                Console::println( Console::inColor('bold_blue', ' + Test Case #' . $testCase->shortName) . ' - '. colorResult($testCase->result) );

                if( $testCase->result == PASSED || $testCase->result == FAILED ) {
                    Console::println(
                        '   ' . Console::inColor('dark_gray',
                        'Tests('. $total .': '. $passed .' PASSED / '. $failed .' FAILED), '
                        .'Assertions('. $totalAssertions .': '. $passedAssertions .' PASSED / '. $failedAssertions .' FAILED)'
                        )
                    );
                }
                else {
                    Console::println(
                        '   ' . Console::inColor('dark_gray',  $testCase->message)
                    );
                }

                // TESTS
                foreach($testCase->tests as $test) {
                    // check reporting mode
                    if( ($test->result == PASSED) && ($this->reportAll == false) )
                        return;

                    Console::println(
                        '    -> ' . Console::inColor('cyan', $test->name) . ' - ' . colorResult($test->result) . ' - ' .
                        Console::inColor('dark_gray',
                            $test->failure ?
                            $test->failure->getMessage() :
                            'Assertions(' . count($test->getPassed()) . ')'
                        )
                    );
                }
            }
        }

        Console::println(' ');
        Console::println('Test Complete.');
        Console::println(' ');
    }
}
function colorResult($result) {
    switch($result) {
        case PASSED:
            return Console::inColor('bold_green', $result);
            break;

        case FAILED:
            return Console::inColor('bold_red', $result);
            break;

        case PENDING:
        case SKIPPED:
            return Console::inColor('yellow', $result);
            break;

        case INCOMPLETE:
            return Console::inColor('purple', $result);
            break;
    }
}
?>
