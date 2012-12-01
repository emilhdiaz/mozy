<?php
namespace Mozy\Core;

/**
 * All object hierarchies in the framework stem from this base class.
 * @author Emil H. Diaz
 * @version 1.0
 * @copyright Mozy Framework
 */
abstract class Object {
    use Getters;
    use Setters;
    use Callers;
    use StaticCallers;

    protected $class;

    private function __construct() {}

    public static function bootstrap() {}

    public function __sleep() {
        $serial = [];
        $properties = $this->class->properties;
        foreach($properties as $property) {
            if( $property->name == 'class' )
                continue;

            $serial[] = $property->name;
        }
        return $serial;
    }

    public function __toString() {
        return $this->class->name . '('.spl_object_hash($this).')';
    }
}
?>