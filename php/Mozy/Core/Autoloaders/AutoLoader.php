<?php
namespace Mozy\Core;

class AutoLoader extends Object implements Singleton {
    
    /**
     * Manually search for a class or interface using the registered loaders. 
     */
    public function load($className) {
        spl_autoload_call($className);
    }

    /**
     * Register a loader. 
     */
    public function registerLoader(Loader $loader) {
        spl_autoload_register([$loader, 'load'], true);
    }    
    
    /**
     * Unregister a loader.
     */
    public function unregisterLoader(Loader $loader) {
        spl_autoload_unregister([$loader, 'load'], true);
    }
}
?>