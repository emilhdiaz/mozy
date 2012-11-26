<?php
namespace Mozy\Core;

require_once('Mozy/Core/Framework.php');

Framework::init();

$framework->processExchange();

#Console::printArray(get_declared_traits());
?>