<?php
namespace Mozy\Core\Reflection;

final class ReflectionMethod extends \ReflectionMethod implements Documented {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $allow;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
        $this->allow = _A($this->comment->annotation('allow'));

        $namespace = $this->declaringClass->namespace;
        foreach($this->allow as &$class) {
            $class = ($class == 'all' ? 'all' : $namespace->class($class)->name);
        }
    }

    public function getDeclaringClass() {
        return ReflectionClass::construct($this->class);
    }

    public function getPrototype() {
        return ReflectionMethod::construct(parent::getPrototype()->class, $this->name);
    }

    public function getParameter($name) {
        return ReflectionParameter::construct($this->class, $this->name, $name);
    }

    public function getParameters() {
        $parameters = [];
        foreach(parent::getParameters() as $parameter) {
            $parameters[$parameter->name] = $this->parameter($parameter->name);
        }
        return $parameters;
    }

    public function getComment() {
        return ReflectionComment::construct($this);
    }

    public function isRestricted() {
        return (bool) count($this->allow);
    }

    public function isDeclaringClass( $class ) {
        return (bool) ($class == $this->declaringClass->name);
    }

    public function isAllowedFor( $class ) {
        $declaringClass = $this->declaringClass;

        // allow if public
        if( $this->isPublic() )
            return true;

        // allow the declaring class
        if( $this->isDeclaringClass($class) )
            return true;

        // allow if all classes are allowed
        if( in_array('all', $this->allow) )
            return true;

        // allow if in list of allowed classes
        if( in_array($class, $this->allow) )
            return true;

        // allow subclasses if protected
        if( $this->isProtected() && ($declaringClass->isAncestorOf($class)) )
            return true;

        return false;
    }
}
?>