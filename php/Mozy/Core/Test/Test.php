<?php
namespace Mozy\Core\Test;

use Mozy\Core\Object;
use Mozy\Core\Exception;

class Test extends Object implements Testable {
    use Assertive;

    protected $name;
    protected $testCase;
    protected $fixture;
    protected $input;
    protected $failure;
    protected $result = PENDING;

    protected function __construct(TestCase $testCase, $input) {
        $this->name     = $testCase->shortName . _S(_A($input));
        $this->testCase = $testCase;
        $this->fixture  = $testCase->testScenario->fixture;
        $this->input    = _A($input);
    }

    public function __toString() {
        return $this->name;
    }

    public function run() {
        try{
            $test = $this->testCase->prototype->bindTo($this, $this);

            // turn on output buffering
            ob_start();
            ob_clean();

            call_user_func_array($test, $this->input);

            // check for expected output
            if( $this->testCase->expectedOutput )
                $this->assertOutput($this->testCase->expectedOutput);

            //check for expected exception
            if( $this->testCase->expectedException )
                $this->assertException(null, $this->testCase->expectedException);

            // finished with test defined assertions
            $this->record = false;

            // check for unexpected output
            if( !$this->testCase->expectedOutput )
                $this->assertNoOutput();
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
                if( $this->testCase->expectedOutput )
                    $this->assertOutput($this->testCase->expectedOutput);

                //check for expected exception
                if( $this->testCase->expectedException )
                    $this->assertException($e, $this->testCase->expectedException);

                // finished with test defined assertions
                $this->record = false;

                // check for unexpected output
                if( !$this->testCase->expectedOutput )
                    $this->assertNoOutput();

                // check for unexpected exception
                if( !$this->testCase->expectedException )
                    $this->assertNoException($e);
            }
            // check for failures
            catch( TestFailureException $e ) {
                $this->result = FAILED;
                $this->failure = $e;
                return;
            }
        }

        // congrats!! test passed
        if( count($this->passed) > 0 )
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