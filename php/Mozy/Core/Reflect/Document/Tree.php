<?php
namespace Mozy\Core\Reflect\Document;

use Mozy\Core\Object;

class Tree extends Object {

	protected $topNode;
	protected $treeNodes	= [];
	protected $orphanNodes	= [];

	protected function __construct( $topElement ) {
		$topNode = TreeNode::construct( $topElement );
		$this->treeNodes[$topElement->name] = $topNode;
		$this->topNode = $topNode;
	}

	public function addNode( $element ) {
		/* Check if element was already processed */
		if ( array_key_exists($element->name, $this->treeNodes) )
			return $this->treeNodes[$element->name];

		/* Create the node */
		$node = TreeNode::construct( $element );
		$this->treeNodes[$element->name] = $node;

		/* Child Node */
		if ( $parentElement = $element->getParentClass() )
			$node->parent = $this->addNode($parentElement);

		/* Top Node */
		elseif ( $element->name != $this->topNode->element->name )
			$this->orphanNodes[] = $node;

		return $node;
	}
}
?>