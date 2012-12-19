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

	protected $uid;
    protected $class;

    private function __construct() {}

    public static function bootstrap() {}

    public function __sleep() {
        #TODO: prevent Singletons from being serialized
        $serial = [];

        $properties = array_keys(get_class_vars(get_called_class()));
        foreach($properties as $property) {
            if ( $property == 'class' )
                continue;

            $serial[] = $property;
        }
        return $serial;
    }

    public function __toString() {
        return $this->class->name . ":" . $this->uid;
    }
}
?>