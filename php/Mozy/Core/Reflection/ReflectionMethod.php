<?php
namespace Mozy\Core\Reflection;

class ReflectionMethod extends \ReflectionMethod implements Documented {

    public function getDeclaringClass() {
        return new ReflectionClass($this->class);
    }
    
    public function getPrototype() {
        return ReflectionMethod(parent::getPrototype()->class, $this->name);
    }
    
    public function getParameter($name) {
        return new ReflectionParameter($this->class, $this->name, $name);
    }
    
    public function getParameters() {
        $parameters = [];
        foreach(parent::getParameters() as $parameter) {
            $parameters[$parameter->name] = $this->getParameter($parameter->name);
        }
        return $parameters;
    }

    public function getComment() {
        return new ReflectionComment($this);
    }
}
?>