<?php
namespace Mozy\Core\Reflection;

use Mozy\Core;

final class ReflectionMethod extends \ReflectionMethod implements Documented {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $restricted;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
        $this->restricted = Core\_A($this->comment->annotation('restricted'));

        $namespace = $this->declaringClass->namespace;
        foreach($this->restricted as &$class) {
            $class = $namespace->class($class)->name;
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
        return (bool) count($this->restricted);
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

        // allow if in list of restricted classes
        if( is_array($this->restricted) && in_array($class, $this->restricted) )
            return true;

        // allow if is (single) restricted class
        if( $this->restricted == $class )
            return true;

        // allow subclasses if protected
        if( $this->isProtected() && ($declaringClass->isAncestorOf($class)) )
            return true;

        return false;
    }
}
?>