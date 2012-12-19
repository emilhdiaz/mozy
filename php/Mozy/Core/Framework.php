<?php
namespace Mozy\Core;

use Mozy\Core\System\System;

define('ROOT', getcwd() . DIRECTORY_SEPARATOR);
define('NAMESPACE_SEPARATOR', '\\');
define('PHP_TAB', "\t");

const DEBUG = false;

require_once(ROOT.'/Mozy/common.php');
require_once('Autoloader.php');

global $framework, $process, $STDIN, $STDOUT, $STDERR;

spl_autoload_register( ['Mozy\Core\AutoLoader', 'load'], true );

final class Framework extends Object implements Singleton {

    private static $self;
    protected $version = 1.0;
    protected $currentRequest;

    public static function init() {
        global $framework, $system, $process;

        // bootstrap the PHP environment
        self::bootstrap();

		// initialize the system and the current process
        $process = System::construct()->process;

        // initialize the framework
        $framework = self::construct();
    }

    public static function bootstrap() {
        /* Configure Error and Logging runtime cnfigurations */
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('display_errors', 'stderr');            //default false
        ini_set('display_startup_errors', true);        //default false
        ini_set('log_errors', false);                   //default true
        ini_set('log_errors_max_len', 1024);            //default 1024
        ini_set('ignore_repeated_errors', false);       //default false
        ini_set('ignore_repeated_source', false);       //default false
        ini_set('report_memleaks', true);               //default true
        ini_set('track_errors', false);                 //default false
        ini_set('html_errors', false);                  //default false
        ini_set('xmlrpc_errors', false);                //default false

        /* Register timezone */
        date_default_timezone_set('America/New_York');

        // configure Error and Exception Handlers
#        set_error_handler( 'Mozy\Core\errorHandler' );
#        set_exception_handler( 'Mozy\Core\exceptionHandler' );
#        register_shutdown_function('Mozy\Core\fatalErrorHandler');
        assert_options(ASSERT_WARNING, FALSE);
    }

    public function callAPI($api, $action, $arguments) {
        $api = 'Mozy\APIs\\'.$api.'API';

        if ( !class_exists($api) ) {
            #TODO throw API level exception
            throw new Exception($api);
        }

        $api = $api::construct();

        if ( !$api->class->hasMethod($action) ) {
            #TODO throw API level exception
            throw new Exception($action);
        }

        $method = $api->class->method($action);
        $parameters = [];

        foreach( $method->parameters as $parameter ) {
            #TODO check argument types
            $value = array_value($arguments, $parameter->name) ?: array_value($arguments, $parameter->position);

			// check if a default is available
			if ( !$value ) {
				if ( $parameter->isDefaultValueAvailable() )
					$value = $parameter->defaultValue;

				else
					#TODO throw API level exception
                	throw new Exception("Required API argument missing: " . $parameter->name);
			}

            $parameters[$parameter->name] = $value;
        }

        $result = $method->invokeArgs($api, $parameters);

        return $result;
    }

    public function getDependencyManager() {
        return DependencyManager::construct();
    }

    public function getVersion() {
        return _S($this->version);
    }

    public function getGateway() {
        return defined('STDIN') ? 'CLI' : 'CGI';
    }

    public function getEndpoint() {
        switch( $this->gateway ) {
            case 'CLI':
                return $_SERVER['SCRIPT_NAME'];
                break;

            case 'CGI':
                break;
        }
    }
}

function errorHandler($code, $msg, $file, $line, $errcontext) {
    global $process;
#    if ( preg_match('/^Missing argument 1 for Mozy\\\Core\\\Object::__construct.*/', $msg) )
#        return;

    #TODO: E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR
    if ( $code & ( E_ERROR ) ) {
#        if ( preg_match(ClassNotFoundError::REGEX, $msg) )
#            exceptionHandler(new ClassNotFoundError($msg, null, $file, $line));
#
#        if ( preg_match(Exception::InterfaceNotFoundRegex, $msg) )
#            exceptionHandler(new InterfaceNotFoundException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::TraitNotFoundRegex, $msg) )
#            exceptionHandler(new TraitNotFoundException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::AbstractDefinitionRegex, $msg) )
#            exceptionHandler(new AbstractDefinitionException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::MissingImplementationRegex, $msg) )
#            exceptionHandler(new MissingImplementationException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UndefinedMethodRegex, $msg) )
#            exceptionHandler(new UndefinedMethodException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UnauthorizedMethodAccessRegex, $msg) )
#            exceptionHandler(new UnauthorizedMethodAccessException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UnauthorizedPropertyAccessRegex, $msg) )
#            exceptionHandler(new UnauthorizedPropertyAccessException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::MissingArgumentRegex, $msg) )
#            exceptionHandler(new MissingArgumentException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::InvalidArgumentTypeRegex, $msg) )
#            exceptionHandler(new InvalidArgumentTypeException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UndefinedConstantRegex, $msg) )
#            exceptionHandler(new UndefinedConstantException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::InvalidConstructionRegex, $msg) )
#            exceptionHandler(new InvalidConstructionException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::NullDereference2Regex, $msg) )
#            exceptionHandler(new NullReferenceException($msg, null, $file, $line));
    }

    if ( $code & ( E_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) ) {
#        if ( preg_match(Exception::DivisionByZeroRegex, $msg) )
#            exceptionHandler(new DivisionByZeroException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::MissingArgument2Regex, $msg) )
#            exceptionHandler(new MissingArgumentException($msg, null, $file, $line));
    }

    if ( $code & ( E_NOTICE | E_USER_NOTICE | E_STRICT ) ) {
#        if ( preg_match(Exception::InvalidArrayKeyRegex, $msg) )
#            exceptionHandler(new InvalidArrayKeyException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::InvalidStringOffsetRegex, $msg) )
#            exceptionHandler(new InvalidStringOffsetException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::NullReferenceRegex, $msg) )
#            exceptionHandler(new NullReferenceException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::NullDereferenceRegex, $msg) )
#            exceptionHandler(new NullReferenceException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UndefinedPropertyRegex, $msg) )
#            exceptionHandler(new UndefinedPropertyException($msg, null, $file, $line));
#
#        if ( preg_match(Exception::UndefinedConstantRegex, $msg) )
#            exceptionHandler(new UndefinedConstantException($msg, null, $file, $line));
    }

	$text = "FATAL ERROR: " . FriendlyErrorType($code) ."- $msg in file $file on line $line";
	exceptionHandler( new \Exception($text) );
}

function exceptionHandler(\Exception $exception) {
    global $process;
    $process->err->writeLine($exception);
    exit($exception->getCode());
}

function fatalErrorHandler() {
	if ( $e = error_get_last() ) {
		$code = $e['type'];
		$msg  = $e['message'];
		$file = $e['file'];
		$line = $e['line'];
		$text = "FATAL ERROR: " . FriendlyErrorType($code) ."- $msg in file $file on line $line";
		exceptionHandler( new \Exception($text) );
    }
}
?>