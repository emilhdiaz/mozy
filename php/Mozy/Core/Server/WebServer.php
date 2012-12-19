<?php
namespace Mozy\Core\Server;

class WebServer extends Server {

	protected function __construct() {
		$this->name 	= $_SERVER['SERVER_NAME'];
		$this->address 	= $_SERVER['SERVER_ADDR'];
		$this->port		= $_SERVER['SERVER_PORT'];
		$this->admin 	= $_SERVER['SERVER_ADMIN'];
		$this->root		= $_SERVER['DOCUMENT_ROOT'];
		$this->signature= $_SERVER['SERVER_SIGNATURE'];
	}

	public function start() {

	}

	public function restart() {

	}

	public function stop() {

	}
}
#IGNORE LIST:
    // - PHP_SELF
    // - argv
    // - argc
    // - GATEWAY_INTERFACE
    // - SERVER_SOFTWARE
    // - SERVER_PROTOCOL
    // - REQUEST_METHOD
    // - REQUEST_TIME
    // - REQUEST_TIME_FLOAT
    // - QUERY_STRING
    // - HTTP_ACCEPT
    // - HTTP_ACCEPT_CHARSET
    // - HTTP_ACCEPT_ENCODING
    // - HTTP_ACCEPT_LANGUAGE
    // - HTTP_CONNECTION
    // - HTTP_HOST
    // - HTTP_REFERER
    // - HTTP_USER_AGENT
    // - HTTPS
    // - REMOTE_ADDR
    // - REMOTE_HOST
    // - REMOTE_PORT
    // - REMOTE_USER
    // - REDIRECT_REMOTE_USER
    // - SCRIPT_FILENAME
    // - PATH_TRANSLATED
    // - SCRIPT_NAME
    // - REQUEST_URI
    // - PHP_AUTH_DIGEST
    // - PHP_AUTH_USER
    // - AUTH_TYPE
    // - PATH_INFO
    // - ORIG_PATH_INFO
?>