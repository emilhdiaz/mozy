<?php
namespace Mozy\Core\Test;

interface Testable {

    public function run();

    public function getResult();

    public function getPassed();

    public function getFailed();
}
?>