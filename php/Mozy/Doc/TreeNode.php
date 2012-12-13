<?php
namespace Mozy\Doc;

use Mozy\Core\Object;

class TreeNode extends Object {

	protected $element;
	protected $parent;
	protected $children = [];

	protected function __construct( $element ) {
		$this->element	= $element;
	}

	public function getName() {
		return $this->element->name;
	}

	public function setParent( TreeNode $parent ) {
#		out("Setting ".$parent->name." as parent of ".$this->name);
		$this->parent = $parent;
		$parent->addChild($this);
	}

	public function addChild( TreeNode $child ) {
		$this->children[$child->name] = $child;
	}

	public function __toString() {
		static $hit = 0;
		$hit++;
		$delim = str_repeat("\t", $hit) . '-> ';

		$string = $this->name . PHP_EOL;
		ksort($this->children, SORT_NATURAL);
		foreach( $this->children as $child ) {
			$string .= $delim . $child;
		}

		$hit--;
		return $string;
	}
}
?>