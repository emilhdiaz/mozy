<?php
use Mozy\Core\Framework;
use Mozy\Core\System\System;

require_once('Mozy/Core/Framework.php');

Framework::init();


$system = System::construct();

var_dump( $system->groupByGID(5219) );


#$framework->processExchange();
?>