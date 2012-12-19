<?php
namespace Mozy\Core\Reflection;

trait Immutability {

    protected static $instances = [];

    public static function construct() {
        $args = func_get_args();
        $key = implode('::', $args);

        // created the reflector that was asked for
        if ( !array_key_exists($key, self::$instances) )
            self::$instances[$key] = self::$reflector->newInstanceArgs($args);

        return self::$instances[$key];
    }
}
?>