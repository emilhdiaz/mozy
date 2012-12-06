#!/usr/bin/env php
<?php
use Mozy\Core\Framework;
use Mozy\Core\System\System;
use Mozy\Core\System\InternalCommand;
use Mozy\Core\System\ExternalCommand;

require_once('Mozy/Core/Framework.php');

Framework::init();

$system = System::construct();
$me = $system->process;
$me->out->write( "Starting parent process with PID(".$me->id.")" );

$calculate_age = function( DateTime $dob ) {
    $now = new DateTime("now");
    $age = $now->diff($dob)->format('%y');
    return $age;
};

$callback = function($age) {
    print "function calculate_age() says I am " . $age . " year(s) old.\n";
};

$name = 'Mozy Application Server';

/* Daemonize the process */
#$system->process->daemonize($name);
#while( true ) {
#    sleep(5);
#}

/* Execute Internal Async Command */
$command = InternalCommand::construct( $calculate_age, new DateTime('8/12/1987') );
$process = $system->process->executeAsynchronous( $command, $callback );

/* Execute External Async Command */
#$command = ExternalCommand::construct('php/scrap.php');
#$process->out->readLine();
#$process->in->write('8/12/1987');
#$process->out->readLine();

#$system->process->waitForChildren();
#$me->out->write( "Parent script terminating now... bye bye!" );

#$system->process->quit();
#$system->process->close();
#$system->process->terminate();
#$system->process->kill();

#$framework->processExchange();
?>