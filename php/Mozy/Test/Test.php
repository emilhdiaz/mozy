<?php
namespace Mozy\Test;

use Mozy\Core\Object;
use Mozy\Core\Exception;
use Mozy\Core\System\Command;

class Test extends Object implements Testable {
    use Assertive;

    protected $name;
    protected $testCase;
    protected $fixture;
    protected $input;
    protected $failure;
    protected $result = PENDING;
    protected $separateProcesses = false;

    protected function __construct(TestCase $testCase, $input) {
        $this->name     = $testCase->shortName . argument_string($input);
        $this->testCase = $testCase;
        $this->input    = _A($input);
        $this->fixture  = $testCase->testScenario->fixture;
        $this->separateProcesses = $testCase->testScenario->separateProcesses;
    }

    public function run() {
        global $process;

		// start the output buffer
		$process->out->buffer();

        try{
            $test = $this->testCase->prototype->bindTo($this, $this);

            if ( $this->separateProcesses ) {
                $localTest;
                $command = Command::construct( $test, $this->input );
                $process->executeAsynchronous( $command, function( $remoteTest ) use( &$localTest ) {
                    $localTest = $remoteTest;
                });

                $process->waitForChildren();

                if ( $localTest instanceOf Exception )
                    throw $localTest->copy();

                $this->failure = $localTest->failure;
                $this->result = $localTest->result;
                $this->assertions = $localTest->assertions;
            }
            else {
                call_user_func_array($test, $this->input);

                // check for expected output
                if ( $this->testCase->expectedOutput )
                    $this->assertOutput($this->testCase->expectedOutput);

                //check for expected exception
                if ( $this->testCase->expectedException )
                    $this->assertException(null, $this->testCase->expectedException);

                // finished with test defined assertions
                $this->record = false;

                // check for unexpected output
                if ( !$this->testCase->expectedOutput )
                    $this->assertNoOutput();
            }
        }
        // check for failures
        catch( TestFailureException $e ) {
            $this->result = FAILED;
            $this->failure = $e;
            return;
        }
        // enter alternate exception processing
        catch( Exception $e ) {
            try {
                // check for expected output
                if ( $this->testCase->expectedOutput )
                    $this->assertOutput($this->testCase->expectedOutput);

                //check for expected exception
                if ( $this->testCase->expectedException )
                    $this->assertException($e, $this->testCase->expectedException);

                // finished with test defined assertions
                $this->record = false;

                // check for unexpected output
                if ( !$this->testCase->expectedOutput )
                    $this->assertNoOutput();

                // check for unexpected exception
                if ( !$this->testCase->expectedException )
                    $this->assertNoException($e);
            }
            // check for failures
            catch( TestFailureException $e ) {
                $this->result = FAILED;
                $this->failure = $e;
                return;
            }
        }

		// end the output buffer
		$process->out->clean()->end();

        // congrats!! test passed
        if ( count($this->passed) > 0 )
            $this->result = PASSED;
    }

    public function getResult() {
        return $this->result;
    }

    public function getPassed() {
        return $this->passedAssertions;
    }

    public function getFailed() {
        return $this->failedAssertions;
    }
}
?>