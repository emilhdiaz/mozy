<?php
namespace Mozy\APIs;

use Mozy\Core\Reflect\Document\Tree;
use Mozy\Core\Reflect\Document\TreeNode;
use Mozy\Core\System\Console\Console;
use Mozy\Core\System\Console\ConsoleOutput;
use Mozy\Core\System\Console\ConsoleView;

class DocumentorConsoleView extends ConsoleView {

	protected function __construct( Console $console ) {
		$this->console = $console;
	}

    public function Definition() {
		global $process;

		$process->out->buffer();

    	$output = $this->console->output;
    	$output->enableSourceHighlighting();

    	$process->out->flush()->end();
    }

    public function classInheritance( Tree $tree ) {
		$this->printTree($tree, 'Class Inheritance');
    }

	public function exceptionInheritance( Tree $tree ) {
		$this->printTree($tree, 'Exception Inheritance');
	}

    private function printTree( Tree $tree, $name ) {
    	global $process;

		$process->out->buffer();

    	$output = $this->console->output;

    	$output->line($name . ' Tree', 'bold', 'cyan');

    	$this->printTreeNode($tree->topNode, $output);

    	$output->nl();
		$output->line("Orphans: " . (empty($tree->orphanNodes) ? 'none' : count($tree->orphanNodes)),  'bold', 'cyan');

		foreach( $tree->orphanNodes as $orphan ) {
			$this->printTreeNode($orphan, $output);
		}

		$process->out->flush()->end();
    }

    private function printTreeNode( TreeNode $node, $output, $level = 0 ) {
		$level++;
		$delim = str_repeat(PHP_TAB, $level) . '-> ';

		if( $level == 1 )
			$output->line("Root: " . $node->name, 'bold', 'yellow');
		else {
			if( ($level % 2) == 0 )
				$output->line($node->name, 'normal', 'yellow');
			else
				$output->line($node->name, 'normal', 'green');
		}

		$children = $node->children;
		ksort($children, SORT_NATURAL);

		foreach( $children as $child ) {
			$output->text($delim, 'bold', 'cyan');
			$this->printTreeNode($child, $output, $level);
		}

		$level--;
    }
}
?>