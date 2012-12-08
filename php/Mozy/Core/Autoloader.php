<?php
namespace Mozy\Core;

abstract class Autoloader {

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
        $namespacePath = get_namespace_path( $resource );

        foreach( static::$namespaceDirectories as $directory ) {
            registerClassPath( $namespacePath . $directory );
        }

        /* Locate File In Namespace Path */
        foreach( static::$extensions as $extension ) {
            $fullFilePath = stream_resolve_include_path( $resourceName . $extension );

            /* Check if Resource Exists */
            if( $fullFilePath ) {
                include_once($fullFilePath);

                /* Bootstrap Class */
                if( class_exists($resource) && method_exists($resource, 'bootstrap') )
                    $resource::bootstrap();
                return;
            }
        }
    }

    /**
     * Register all file extensions replacing previous registrations.
     * Return array of registered file extensions.
     */
    public static function extensions(array $extensions = null) {
        if( $extensions ) {
            static::$extensions = array_unique($extensions);
        }
        return static::$extensions;
    }

    /**
     *  Register a single file extension.
     */
    public static function registerExtension($extension) {
        if( !in_array($extension, static::$extensions) ) {
            static::$extensions[] = $extension;
        }
    }

    /**
     * Unregister a single file extension.
     */
    public static function unregisterExtension($extension) {
        if( ($key = array_search($del_val, $messages)) !== false) {
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