<?php
namespace Mozy\APIs;

use Mozy\Core\API;
use Mozy\Core\Reflect\Document\Documentor;

class DocumentorAPI extends API {

	public function Definition( $resourceName ) {
		$doc = Documentor::construct();
		return $doc->definition( $resourceName );
	}

    public function classInheritance( $root = 'Mozy\Core\Object' ) {
        global $framework;
        $doc = Documentor::construct();
		$doc->discoverResources( $framework->class->namespace->name );
		return $doc->classInheritance( $root );
    }

    public function exceptionInheritance( $root = 'Mozy\Core\Exception' ) {
        global $framework;
        $doc = Documentor::construct();
		$doc->discoverResources( $framework->class->namespace->name );
		return $doc->exceptionInheritance( $root );
    }
}
?>