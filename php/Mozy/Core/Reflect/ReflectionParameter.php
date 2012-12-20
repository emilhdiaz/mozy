<?php
namespace Mozy\Core\Reflect;

final class ReflectionParameter extends \ReflectionParameter {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $declaringClass;
    protected $method;

    public function __construct($class, $method, $parameter) {
        parent::__construct([$class, $method], $parameter);
        $this->declaringClass = $class;
        $this->method = $method;
    }

    public function getDeclaringClass() {
        return ReflectionClass::construct($this->declaringClass);
    }

    public function getDeclaringMethod() {
        return ReflectionMethod::construct($this->declaringClass, $this->method);
    }

    public function getType() {
        $className;
        $class = parent::getClass();

        // check annotations
        if ( !$class ) {
            $comment = $this->declaringMethod->docComment;
            preg_match('/@var\s+'.$this->name.'\s+(\S+)[\r\n]/', $comment, $matches);
            $className = count($matches) > 0 ? $matches[1] : null;
        }
        else {
            $className = $class->name;
        }

        if ( !$className )
            return;

        return ReflectionClass::construct($className);
    }
}
?>