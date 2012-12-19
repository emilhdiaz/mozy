<?php
namespace Mozy\Doc;

use Mozy\Core\Object;
use Mozy\Core\Singleton;
use Mozy\Core\Reflection\ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Documentor extends Object implements Singleton {

	protected $namespaces	= [];
	protected $interfaces	= [];
	protected $traits		= [];
	protected $classes		= [];
	protected $exceptions	= [];
	protected $reflectors	= [];
	protected $resources	= [];

	public function addResource( $resourceName ) {
		if ( !array_key_exists($resourceName, $this->resources) ) {

			$resource = ReflectionClass::construct( $resourceName );

			if( strpos($resourceName, 'Test') !== false )
				println($resourceName);

			/* Classify the resource */
			if ( $resource->isInterface() )
				$this->interfaces[$resourceName] = $resource;

			else if ( $resource->isTrait() )
				$this->traits[$resourceName] = $resource;

			else if ( $resource->isException() )
				$this->exceptions[$resourceName] = $resource;

			else if ( $resource->isReflector() )
				$this->reflectors[$resourceName] = $resource;

			else
				$this->classes[$resourceName] = $resource;

			$this->resources[$resourceName] = $resource;
		}
		return $this->resources[$resourceName];
	}

    public function discoverResources($namespace) {
        try{
            $path = get_path_namespace($namespace);
            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::CURRENT_AS_FILEINFO | RecursiveDirectoryIterator::SKIP_DOTS);
            $directory = new RecursiveIteratorIterator($directory);

            foreach( $directory as $filePath ) {
                if ( !($resource = get_class_from_filename($filePath)) ) {
   	            	continue;
                }

                if ( $resource )
                	$this->addResource( $resource );
            }
        } catch (\UnexpectedValueException $e) {
            throw $e;
        }
        $this->namespaces[] = $namespace;
        return $this;
    }

    public function getDefinition( $resourceName ) {
    }

    public function getClassInheritance( $root = 'Mozy\Core\Object' ) {
    	$inheritanceTree = Tree::construct( $this->addResource($root) );
    	foreach( $this->classes as $class ) {
    		$inheritanceTree->addNode( $class );
    	}
		return $inheritanceTree;
    }

    public function getExceptionInheritance( $root = 'Mozy\Core\Exception' ) {
    	$inheritanceTree = Tree::construct( $this->addResource($root) );
    	foreach( $this->exceptions as $exception ) {
    		$inheritanceTree->addNode( $exception );
    	}
		return $inheritanceTree;
    }
}
?>