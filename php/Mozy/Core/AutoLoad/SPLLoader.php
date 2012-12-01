<?php
namespace Mozy\Core\AutoLoad;

class SPLLoader extends Loader {

    public function load($className) {
        spl_autoload($className);
    }

    public function extensions(array $extensions = null) {
        $ext = parent::extensions($extensions);
        return spl_autoload_extensions($ext);
    }

    public function registerExtension($extension) {
        parent::registerExtension($extension);
        spl_autoload_extensions($this->extensions());
    }

    public function unregisterExtension($extension) {
        parent::unregisterExtension($extension);
        spl_autoload_extensions($this->extensions());
    }
}
?>