<?php
namespace Mozy\Core\Reflection;

use Mozy\Core;

trait Bootstrap {

    public static function bootstrap() {
        // create the reflector's own reflector
        static::$reflector = new ReflectionClass(get_called_class());
    }
}
?>