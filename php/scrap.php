<?php
use Mozy\Core\Object;
use Mozy\Core\AsyncCallers;
use Mozy\Core\System\System;
use Mozy\Core\System\Command;

class Test extends Object {
    use AsyncCallers;

    /**
     * @allow all
     */
    protected function calculateAge( DateTime $dob, Closure $callback = null ) {
        $now = new DateTime("now");
        $age = $now->diff($dob)->format('%y');
        return $age;
    }

    /**
     * @allow all
     * @async
     */
    protected function calculateAgeAsync( DateTime $dob, Closure $callback = null ) {
        $now = new DateTime("now");
        $age = $now->diff($dob)->format('%y');
        sleep(1);
        return $age;
    }
}

$system = System::construct();
$me = $system->process;
#$me->out->writeLine( "Starting parent process with PID(".$me->id.")" );

$test = Test::construct();

// synchronous call with return value
debug("Synchronous call says I am " . $test->calculateAge(new DateTime('8/12/1987')) . " year(s) old.");

// synchronous call with callback
$test->calculateAge(new DateTime('8/12/1987'), function($age) {
    debug("Synchronous callback says I am " . $age . " year(s) old.");
});

// asynchronous call with callback
$test->calculateAgeAsync(new DateTime('8/12/1987'), function($age) {
    debug("Asynchronous callback says I am " . $age . " year(s) old.");
});
debug("Make sure im not waiting for asynchronous callback");

/* Daemonize the process */
#$system->process->daemonize('Mozy Application Server');
#while( true ) {
#    sleep(5);
#}

/* Execute Internal Async Command */
#$command = Command::construct( $calculate_age, new DateTime('8/12/1987') );
#$process = $system->process->executeAsynchronous( $command, $callback );

#$calculate_age(new DateTime('8/12/1987'), );

/* Execute External Async Command */
#$command = Command::construct('php/scrap.php');
#$process->out->readLine();
#$process->in->writeLine('8/12/1987');
#$process->out->readLine();

#$system->process->waitForChildren();
#$me->out->writeLine( "Parent script terminating now... bye bye!" );

#$system->process->quit();
#$system->process->close();
#$system->process->terminate();
#$system->process->kill();

?>