<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

abstract class Command extends Object {

	public static function construct( $command, $arguments = [], $options = [] ) {
		if( is_callable($command) )
			return InternalCommand::_construct_($command, $arguments);
		else
			return ExternalCommand::_construct_($command, $arguments, $options);
	}

    abstract public function __invoke();
}
?>