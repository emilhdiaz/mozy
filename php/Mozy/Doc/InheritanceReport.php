<?php
namespace Mozy\Doc;

use Mozy\Core\Object;
use Mozy\Core\System\ConsoleOutput;

class InheritanceReport extends Object {

	protected $documentor;
	protected $tree;

    protected function __construct(Documentor $documentor) {
#        $this->documentor = $documentor;
        $this->tree = $documentor->objectTree;
    }

    public function __toText() {
        global $framework;

        $documentor = $this->unitTest;

        $output = ConsoleOutput::construct();

        $output->overrides(PASSED, 'bold', 'green');
        $output->overrides(FAILED, 'bold', 'red');
        $output->overrides(PENDING, 'bold', 'yellow');
        $output->overrides(SKIPPED, 'bold', 'green');
        $output->overrides(INCOMPLETE, 'bold', 'magenta');

        $output->nl();
        $output->line('Class Inheritance Tree Complete.');
        $output->nl();
        return $output;
    }

}
?>