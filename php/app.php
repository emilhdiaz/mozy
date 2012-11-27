<?php
namespace Mozy\Core;

require_once('Mozy/Core/Framework.php');

Framework::init();

#var_dump(serialize(1));

$framework->processExchange();

?>