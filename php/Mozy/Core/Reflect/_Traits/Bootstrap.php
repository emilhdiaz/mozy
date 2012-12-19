<?php
namespace Mozy\Core\Reflect;

trait Bootstrap {

    public static function bootstrap() {
        // create the reflector's own reflector
        static::$reflector = new ReflectionClass(get_called_class());
    }
}
?>