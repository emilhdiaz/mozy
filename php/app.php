<?php
use Mozy\Core\Framework;
use Mozy\Core\System\System;
use Mozy\Core\System\Command;
use Mozy\Core\System\Pipe;

require_once('Mozy/Core/Framework.php');

Framework::init();


$system = System::construct();

$command = Command::construct('php', 'php/scrap.php');

$process = $system->executeAsynchronous( $command );

echo "Child PID: ". $process->id . "\n";

$process->out->blocking = true;
$process->out->readLine();
$process->in->write(25);
$process->out->readLine();

#$system->killChildProcesses();
#$system->waitForChildProcesses();

#$framework->processExchange();
?>