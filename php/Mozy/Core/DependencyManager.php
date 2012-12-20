<?php
namespace Mozy\Core;

class DependencyManager extends Object implements Singleton {

    protected $dependencies;

    protected function __construct() {
        $this->dependencies = [];
    }

    public function isDependencyLoaded($type, $dependency) {
        global $framework;

        switch($type) {
            case 'PHP':
                return (phpversion() == $dependency);
                break;

            case 'Zend':
                return (zend_version() == $dependency);
                break;

            case 'Mozy':
                return ($framework->version == $dependency);
                break;

            case 'extension':
                list($extension, $version) = explode('_', $dependency);
                return (phpversion($extension) == $version);
                break;

            case 'package':
                break;

            case 'class':
                return class_exists($dependency, false);
                break;

            case 'interface':
                return interface_exists($dependency, false);

            case 'trait':
                return trait_exists($dependency, false);
                break;

            case 'method':
                list($class, $method) = explode('::', $dependency);
                return method_exists($class, $method);

            case 'function':
                return function_exists($dependency);
                break;

            default:
                return false;
        }
    }
}
?>