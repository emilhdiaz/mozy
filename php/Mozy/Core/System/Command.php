<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;

abstract class Command extends Object {

	public static function construct( $command, $arguments = [], $options = [] ) {
		if( is_callable($command) )
			return InternalCommand::construct($command, $arguments);
		else
			return ExternalCommand::construct($command, $arguments, $options);
	}

    abstract public function __invoke();
}
?>