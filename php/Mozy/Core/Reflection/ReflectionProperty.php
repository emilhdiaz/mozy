<?php
namespace Mozy\Core\Reflection;

final class ReflectionProperty extends \ReflectionProperty implements Documented {
    use Getters;
    use Callers;
    use Bootstrap;
    use Immutability;

    protected static $reflector;
    protected $type;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
        $this->type = $this->comment->annotation('type');
    }

    public function getDeclaringClass() {
        return ReflectionClass::construct($this->class);
    }

    public function getType() {
        return $this->type;
    }

    public function getComment() {
        return ReflectionComment::construct($this);
    }
}
?>