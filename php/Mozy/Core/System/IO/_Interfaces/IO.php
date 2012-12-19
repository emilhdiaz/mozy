<?php
namespace Mozy\Core\System\IO;

interface IO {

    public function open();

    public function isOpen();

    public function write( $data );

    public function readLine();

    public function setBlocking( $blocking );

    public function close();

    public function remove();
}
?>