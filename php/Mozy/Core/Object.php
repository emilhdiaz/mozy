<?php
namespace Mozy\Core;

/**
 * All object hierarchies in the framework stem from this base class.
 * @author Emil H. Diaz
 * @version 1.0
 * @copyright Mozy Framework
 */
class Object {
    use ApplicationContext;

    protected $class;

    /**
     * Instance constructor. Responsible for initialization of the instance.
     */
    private function __construct($class) {
        $this->class = $class;
    }

    /**
     * Static constructor. Responsible for instantiation of the instance.
     */
    public static function construct() {
        $args = func_get_args();
        $factory = self::factory();
        $class = $factory->reflect(get_called_class());

        /* Singletons Creation */
        if( $class->implementsInterface(Singleton) )
            return $factory->getSingleton($class, $args);

        /* Normal Creation */
        else
            return $factory->getInstance($class, $args);
    }

    /**
     * Static class bootstrap. Responsible for initialization of static class properties.
     */
    public static function bootstrap() {}

    /**
     * Magic Getter
     */
    public function __get( $name ) {
        $getter = 'get' . ucfirst($name);

        // check if getter method exists
        if( $this->class->hasMethod($getter) ) {
            return $this->$getter();
        }

        // check if property exists
        if( !$this->class->hasProperty($name) ) {
            $frame = get_calling_frame($this);
            throw new UndefinedPropertyException($name, null, $frame->getCaller(), $frame->getLine());
        }

        // check if property is restricted
        if( $this->class->getProperty($name)->isRestricted() ) {
            $frame = get_calling_frame($this);
            throw new UnauthorizedPropertyAccessException($name, null, $frame->getCaller(), $frame->getLine());
        }

        return $this->$name;
    }

    /**
     * Magic Setter.
     */
    public function __set( $name, $value ) {
        $setter = 'set' . ucfirst($name);

        // check if setter method exists
        if( $this->class->hasMethod($setter) ) {
            $this->$setter($value);
        }

        // check if property exists
        if( !$this->class->hasProperty($name) ) {
            $frame = get_calling_frame($this);
            throw new UndefinedPropertyException($name, null, $frame->getCaller(), $frame->getLine());
        }

        $frame = get_calling_frame($this);
        throw new UnauthorizedPropertyAccessException($name, null, $frame->getCaller(), $frame->getLine());
    }

    /**
     * Magic string conversion.
     */
    public function __toString() {
        return $this->class->name . '('.spl_object_hash($this).')';
    }

    public function getClass() {
        return $this->class;
    }
}
?>