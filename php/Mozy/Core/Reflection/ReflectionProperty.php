<?php
namespace Mozy\Core\Reflection;

class ReflectionProperty extends \ReflectionProperty implements Documented {

    protected $type;
    protected $restricted;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
        $this->type = $this->getComment()->getAnnotation('type');
        $this->restricted = $this->getComment()->getAnnotation('restricted');
    }

    public function getDeclaringClass() {
        return new ReflectionClass($this->class);
    }

    public function getType() {
        return $this->type;
    }

    public function isRestricted() {
        return $this->restricted;
    }

    public function getComment() {
        return new ReflectionComment($this);
    }
}
?>