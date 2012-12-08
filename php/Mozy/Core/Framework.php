<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\System\System;

require_once('common.php');
require_once('Autoloader.php');

global $framework, $system, $process, $STDIN, $STDOUT, $STDERR;

const Object                = 'Mozy\Core\Object';
const Immutable             = 'Mozy\Core\Immutable';
const Singleton             = 'Mozy\Core\Singleton';
const Factory               = 'Mozy\Core\Factory';
const TestCase              = 'Mozy\Core\Test\TestCase';
const TestScenario          = 'Mozy\Core\Test\TestScenario';
const Assertion             = 'Mozy\Core\Test\Assertion';
const ReflectionNamespace   = 'Mozy\Core\Reflection\ReflectionNamespace';
const ReflectionClass       = 'Mozy\Core\Reflection\ReflectionClass';
const ReflectionMethod      = 'Mozy\Core\Reflection\ReflectionMethod';
const ReflectionProperty    = 'Mozy\Core\Reflection\ReflectionProperty';
const ReflectionParameter   = 'Mozy\Core\Reflection\ReflectionParameter';
const ReflectionComment     = 'Mozy\Core\Reflection\ReflectionComment';
const InternalCommand       = 'Mozy\Core\System\InternalCommand';
const ExternalCommand       = 'Mozy\Core\System\ExternalCommand';

define('ROOT', getcwd() . DIRECTORY_SEPARATOR);
define('NAMESPACE_SEPARATOR', '\\');

spl_autoload_register( ['Mozy\Core\AutoLoader', 'load'], true );

final class Framework extends Object implements Singleton {

    private static $self;
    protected $version = 1.0;
    protected $currentRequest;
    protected $overrideFormat;

    public static function init() {
        global $framework, $system, $process;

        // bootstrap the PHP environment
        self::bootstrap();

        // initialize the framework
        $framework = self::construct();

        $system = System::construct();

        $process = $system->process;
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
        set_error_handler( 'Mozy\Core\errorHandler' );
        set_exception_handler( 'Mozy\Core\exceptionHandler' );
        register_shutdown_function('Mozy\Core\fatalErrorHandler');
        assert_options(ASSERT_WARNING, FALSE);

        /* Turn on output buffering */
        ob_start();
    }

    public function processExchange() {
        global $process;

        // determine the exchange request type
        switch( $this->gateway ) {
            case 'CLI':
                $this->currentRequest = ConsoleRequest::construct();
                break;

            case 'CGI':
                break;
        }

        try {
            $result = $this->callAPI($this->currentRequest->api, $this->currentRequest->action, $this->currentRequest->arguments, $this->currentRequest->format);
        }
        catch(\Exception $e) {
            throw $e;
        }

        /* Send the result */
        $process->out->writeLine($result);
    }

    public function callAPI($api, $action, $arguments, $format = null) {
        $api = 'Mozy\APIs\\'.$api.'API';

        if( !ReflectionClass::exists($api) ) {
            #TODO throw API level exception
            throw new Exception($api);
        }

        $api = $api::construct();

        if( !$api->class->hasMethod($action) ) {
            #TODO throw API level exception
            throw new Exception($action);
        }

        $method = $api->class->method($action);
        $parameters = [];

        foreach( $method->parameters as $parameter ) {
            #TODO check argument types
            $value = array_value($arguments, $parameter->name) ?: array_value($arguments, $parameter->position);

            // check if value is required
            if( !$value && !$parameter->isDefaultValueAvailable() )
                #TODO throw API level exception
                throw new Exception($parameter);

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

    public function setOverrideFormat( $format ) {
        $this->overrideFormat = (string) $format;
    }
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
    global $process;
#    if( preg_match('/^Missing argument 1 for Mozy\\\Core\\\Object::__construct.*/', $errstr) )
#        return;

    #TODO: E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR
    if( $errno & ( E_ERROR ) ) {
        if( preg_match(ClassNotFoundError::REGEX, $errstr) )
            exceptionHandler(new ClassNotFoundError($errstr, null, $errfile, $errline));

#        if( preg_match(Exception::InterfaceNotFoundRegex, $errstr) )
#            exceptionHandler(new InterfaceNotFoundException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::TraitNotFoundRegex, $errstr) )
#            exceptionHandler(new TraitNotFoundException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::AbstractDefinitionRegex, $errstr) )
#            exceptionHandler(new AbstractDefinitionException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::MissingImplementationRegex, $errstr) )
#            exceptionHandler(new MissingImplementationException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UndefinedMethodRegex, $errstr) )
#            exceptionHandler(new UndefinedMethodException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UnauthorizedMethodAccessRegex, $errstr) )
#            exceptionHandler(new UnauthorizedMethodAccessException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UnauthorizedPropertyAccessRegex, $errstr) )
#            exceptionHandler(new UnauthorizedPropertyAccessException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::MissingArgumentRegex, $errstr) )
#            exceptionHandler(new MissingArgumentException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::InvalidArgumentTypeRegex, $errstr) )
#            exceptionHandler(new InvalidArgumentTypeException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UndefinedConstantRegex, $errstr) )
#            exceptionHandler(new UndefinedConstantException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::InvalidConstructionRegex, $errstr) )
#            exceptionHandler(new InvalidConstructionException($errstr, null, $errfile, $errline));
#
#        if ( preg_match(Exception::NullDereference2Regex, $errstr) )
#            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));
    }

    if( $errno & ( E_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) ) {
#        if( preg_match(Exception::DivisionByZeroRegex, $errstr) )
#            exceptionHandler(new DivisionByZeroException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::MissingArgument2Regex, $errstr) )
#            exceptionHandler(new MissingArgumentException($errstr, null, $errfile, $errline));
    }

    if( $errno & ( E_NOTICE | E_USER_NOTICE | E_STRICT ) ) {
#        if( preg_match(Exception::InvalidArrayKeyRegex, $errstr) )
#            exceptionHandler(new InvalidArrayKeyException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::InvalidStringOffsetRegex, $errstr) )
#            exceptionHandler(new InvalidStringOffsetException($errstr, null, $errfile, $errline));
#
#        if ( preg_match(Exception::NullReferenceRegex, $errstr) )
#            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));
#
#        if ( preg_match(Exception::NullDereferenceRegex, $errstr) )
#            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UndefinedPropertyRegex, $errstr) )
#            exceptionHandler(new UndefinedPropertyException($errstr, null, $errfile, $errline));
#
#        if( preg_match(Exception::UndefinedConstantRegex, $errstr) )
#            exceptionHandler(new UndefinedConstantException($errstr, null, $errfile, $errline));
    }

    out("UNHANDLED ERROR: " . FriendlyErrorType($errno) ."- $errstr file $errfile on line $errline");
    die($errno);
}

function exceptionHandler(\Exception $exception) {

    fwrite( fopen('exceptions.log', 'w+'), (string) $exception );

    out($exception);
    exit($exception->getCode());
}

function fatalErrorHandler() {
    try {
        # Getting last error
        $e = error_get_last();
        if( $e ) {
            ob_clean();
            $code = $e['type'];
            $msg  = $e['message'];
            $file = $e['file'];
            $line = $e['line'];
            errorHandler($code,$msg,$file,$line,null);
        }
    }
    catch( Exception $e ) {
        exceptionHandler($e);
    }
}
?>