<?php
namespace Mozy\Core;

abstract class Autoloader {

	public $uid = 0;

    protected static $extensions = ['.php'];

    protected static $namespaceDirectories = [
        '',
        '/_Exceptions',
        '/_Interfaces',
        '/_Tests',
        '/_Traits'
    ];

    public static function load($resource) {
        $parts = explode( NAMESPACE_SEPARATOR, $resource );
        $resourceName = array_pop( $parts );

        /* Prepare Namespace Path */
        restore_include_path();
        $namespace = get_namespace($resource);
        $namespacePath = get_path_namespace( $namespace );

        foreach( static::$namespaceDirectories as $directory ) {
            registerClassPath( $namespacePath . $directory );
        }

        /* Locate File In Namespace Path */
    	$extension = '.php';
    	$filePath = $resourceName . $extension;
        $fullFilePath = stream_resolve_include_path( $filePath );

        /* Check if resource exists with the current extension */
        if ( $fullFilePath ) {
            include_once($fullFilePath);

			/* Looking for a Trait */
            if ( strpos($fullFilePath, '_Traits/') !== false) {
            	if ( !trait_exists($resource, false) )
            		throw new TraitNotFoundError($resource);
            }

            /* Looking for an Interface */
            else if ( strpos($fullFilePath, '_Interfaces/') !== false) {
            	if ( !interface_exists($resource, false) ) {
            		throw new InterfaceNotFoundError($resource);
            	}
            }

            /* Looking for a Class */
            else  {
				if ( !class_exists($resource, false) ) {
					throw new ClassNotFoundError($resource);
				}

            	/* Bootstrap Class */
            	if ( method_exists($resource, 'bootstrap') ) {
                	$resource::bootstrap();
            	}
			}
			return;
        }

        /* At this point the file was not found */
        throw new FileNotFoundError("File not found for resource '$resource'");
    }

    /**
     * Register all file extensions replacing previous registrations.
     * Return array of registered file extensions.
     */
    public static function extensions(array $extensions = null) {
        if ( $extensions ) {
            static::$extensions = array_unique($extensions);
        }
        return static::$extensions;
    }

    /**
     *  Register a single file extension.
     */
    public static function registerExtension($extension) {
        if ( !in_array($extension, static::$extensions) ) {
            static::$extensions[] = $extension;
        }
    }

    /**
     * Unregister a single file extension.
     */
    public static function unregisterExtension($extension) {
        if ( ($key = array_search($del_val, $messages)) !== false) {
            unset($messages[$key]);
        }
    }

    /**
     * Register multiple file extensions.
     */
    public static function registerExtensions(array $extensions) {
        foreach($extensions as $extension) {
            $this->registerExtension($extension);
        }
    }

    /**
     * Unregister multiple file extensions.
     */
    public static function unregisterExtensions(array $extensions) {
        foreach($extensions as $extension) {
            $this->unregisterExtension($extension);
        }
    }
}
?>