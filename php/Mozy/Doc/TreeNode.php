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
		$this->parent = $parent;
		$parent->addChild($this);
	}

	public function addChild( TreeNode $child ) {
		$this->children[$child->name] = $child;
	}
}
?>