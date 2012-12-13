<?php
namespace Mozy\APIs;

use Mozy\Core\API;
use Mozy\Doc\Documentor;

class DocumentorAPI extends API {

	public function Definition( $resourceName ) {
		$doc = Documentor::construct();
		return $doc->definition( $resourceName );
	}

    public function ClassInheritance( $root = 'Mozy\Core\Object' ) {
        global $framework;
        $doc = Documentor::construct();
		$doc->discoverResources( $framework->class->namespace->name );
		return $doc->classInheritance( $root );
    }

    public function ExceptionInheritance( $root = 'Mozy\Core\Exception' ) {
        global $framework;
        $doc = Documentor::construct();
		$doc->discoverResources( $framework->class->namespace->name );
		return $doc->exceptionInheritance( $root );
    }
}
?>