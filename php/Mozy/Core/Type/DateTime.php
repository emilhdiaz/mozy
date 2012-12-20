<?php
namespace Mozy\Core\Type;

use Mozy\Core\Getters;
use Mozy\Core\Setters;
use Mozy\Core\Callers;
use Mozy\Core\StaticCallers;

class DateTime extends \DateTime {
	use Getters;
    use Setters;
    use Callers;
    use StaticCallers;

	protected $uid;
    protected $class;

	public function __toString() {
		return $this->format('M j Y');
	}
}
?>