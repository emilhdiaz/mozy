<?php
namespace Mozy\Core;

trait ApplicationContext {
    public function getFramework() {
        return Framework::construct();
    }

    public function getFactory() {
        return Factory::construct();
    }

    public static function factory() {
        return Factory::construct();
    }

    public function getApplication() {

    }
}
?>