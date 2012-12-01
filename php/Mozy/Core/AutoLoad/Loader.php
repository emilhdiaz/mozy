<?php
namespace Mozy\Core\AutoLoad;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

abstract class Loader extends Object implements Singleton {

    protected $extensions = ['.php'];
    #TODO: replace $extensions array with unique Array object

    abstract public function load($className);

    /**
     * Register all file extensions replacing previous registrations.
     * Return array of registered file extensions.
     */
    public function extensions(array $extensions = null) {
        if( $extensions ) {
            $this->extensions = array_unique($extensions);
        }
        return implode(',',$this->extensions);
    }

    /**
     *  Register a single file extension.
     */
    public function registerExtension($extension) {
        if( !in_array($extension, $this->extensions) ) {
            $this->extensions[] = $extension;
        }
    }

    /**
     * Unregister a single file extension.
     */
    public function unregisterExtension($extension) {
        if( ($key = array_search($del_val, $messages)) !== false) {
            unset($messages[$key]);
        }
    }

    /**
     * Register multiple file extensions.
     */
    public function registerExtensions(array $extensions) {
        foreach($extensions as $extension) {
            $this->registerExtension($extension);
        }
    }

    /**
     * Unregister multiple file extensions.
     */
    public function unregisterExtensions(array $extensions) {
        foreach($extensions as $extension) {
            $this->unregisterExtension($extension);
        }
    }
}
?>