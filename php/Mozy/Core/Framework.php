<?php
namespace Mozy\Core;

use Mozy\Core\AutoLoad\AutoLoader;
use Mozy\Core\AutoLoad\ClassLoader;
use Mozy\Core\AutoLoad\TraitLoader;
use Mozy\Core\AutoLoad\InterfaceLoader;
use Mozy\Core\AutoLoad\ExceptionLoader;
use Mozy\Core\AutoLoad\TestLoader;
use Mozy\Core\Reflection\ReflectionClass;
use Mozy\Core\System\System;


require_once('common.php');

define('ROOT', getcwd().'/');

spl_autoload_register( 'Mozy\Core\coreAutoloader', true );

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

global $framework, $STDIN, $STDOUT, $STDERR;
$STDIN  = STDIN;
$STDOUT = STDOUT;
$STDERR = STDERR;

final class Framework extends Object implements Singleton {

    private static $self;
    protected $version = 1.0;
    protected $system;
#    protected $application;
    protected $allowSubprocess = [];
    protected $maxChildProcesses = 100;

    protected function __construct() {
        // configure Class Path & Autoloaders
        $autoloader = AutoLoader::construct();
        $autoloader->registerLoader( ClassLoader::construct() );
        $autoloader->registerLoader( TraitLoader::construct() );
        $autoloader->registerLoader( InterfaceLoader::construct() );
        $autoloader->registerLoader( ExceptionLoader::construct() );
        $autoloader->registerLoader( TestLoader::construct() );

        $this->system = System::construct();
    }

    public static function init() {
        global $framework;

        // bootstrap the PHP environment
        self::bootstrap();

        // initialize the framework
        $framework = self::construct();

        // write the PID
#        print $framework->console->output->line("PHP Process (".getmypid().")");
    }

    public static function bootstrap() {
        // configure Error and Logging Runtime Configurations
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

        // register timezone
        date_default_timezone_set('America/New_York');

        // configure Error and Exception Handlers
#        set_error_handler( 'Mozy\Core\errorHandler' );
#        set_exception_handler( 'Mozy\Core\exceptionHandler' );
#        register_shutdown_function('Mozy\Core\fatalErrorHandler');
        assert_options(ASSERT_WARNING, FALSE);

        /* Buffer Output */
#        ob_start();
    }

    public function processExchange() {
        // determine the exchange request type
        switch( $this->gateway ) {
            case 'CLI':
                $request = ConsoleRequest::current();
                break;

            case 'CGI':
                break;
        }

        #TODO: switch this to checking a PID file during initialization
        $this->allowSubprocess = (bool) (array_value($request->arguments, 'allowSubprocess') === true);

        try {
            $result = $this->callAPI($request->api, $request->action, $request->arguments, $request->format);
        }
        #TODO: need to handle API Exception here
        catch(Exception $e) {
            throw $e;
        }

        // generate the exchange response type
        switch($request->format) {
            case 'text':
                print $result->__toText();
                break;

            case 'serial':
                print $result->__toSerial();
                break;

            case 'native':
            default:
                print $result->__toText();
                break;
        }
    }

    public function callAPI($api, $action, $arguments, $format = null, $separateProcess = false) {
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

            // dont need to add to command line arguments
            if( !$value && $separateProcess )
                continue;

            $parameters[$parameter->name] = $value;
        }

        ################################################################################
        # VERY DANGEROUS!! Ensure this does not cause an endless loop of child processes
        ################################################################################
        if( $separateProcess && !$this->allowSubprocess ) {
            #TODO throw API level exception
            throw new Exception("Subprocesses not allowed!");
        }

        $result;
        if( $separateProcess ) {
            $request = ConsoleRequest::construct($this->endpoint, $api->name, $action, $parameters);
            $response = $request->send();
            $result = $response;
        }
        else {
            $result = $method->invokeArgs($api, $parameters);
        }
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

    public function printErrorReportingLevels() {
        print $this->console->output->line(FriendlyErrorType(error_reporting()));
    }
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
    if( preg_match('/^Missing argument 1 for Mozy\\\Core\\\Object::__construct.*/', $errstr) )
        return;


    if( $errno & ( E_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR ) ) {
        if( preg_match(Exception::ClassNotFoundRegex, $errstr) )
            exceptionHandler(new ClassNotFoundException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::InterfaceNotFoundRegex, $errstr) )
            exceptionHandler(new InterfaceNotFoundException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::TraitNotFoundRegex, $errstr) )
            exceptionHandler(new TraitNotFoundException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::AbstractDefinitionRegex, $errstr) )
            exceptionHandler(new AbstractDefinitionException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::MissingImplementationRegex, $errstr) )
            exceptionHandler(new MissingImplementationException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UndefinedMethodRegex, $errstr) )
            exceptionHandler(new UndefinedMethodException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UnauthorizedMethodAccessRegex, $errstr) )
            exceptionHandler(new UnauthorizedMethodAccessException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UnauthorizedPropertyAccessRegex, $errstr) )
            exceptionHandler(new UnauthorizedPropertyAccessException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::MissingArgumentRegex, $errstr) )
            exceptionHandler(new MissingArgumentException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::InvalidArgumentTypeRegex, $errstr) )
            exceptionHandler(new InvalidArgumentTypeException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UndefinedConstantRegex, $errstr) )
            exceptionHandler(new UndefinedConstantException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::InvalidConstructionRegex, $errstr) )
            exceptionHandler(new InvalidConstructionException($errstr, null, $errfile, $errline));

        if ( preg_match(Exception::NullDereference2Regex, $errstr) )
            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));
    }

    if( $errno & ( E_WARNING | E_COMPILE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) ) {
        if( preg_match(Exception::DivisionByZeroRegex, $errstr) )
            exceptionHandler(new DivisionByZeroException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::MissingArgument2Regex, $errstr) )
            exceptionHandler(new MissingArgumentException($errstr, null, $errfile, $errline));
    }

    if( $errno & ( E_NOTICE | E_USER_NOTICE | E_STRICT ) ) {
        if( preg_match(Exception::InvalidArrayKeyRegex, $errstr) )
            exceptionHandler(new InvalidArrayKeyException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::InvalidStringOffsetRegex, $errstr) )
            exceptionHandler(new InvalidStringOffsetException($errstr, null, $errfile, $errline));

        if ( preg_match(Exception::NullReferenceRegex, $errstr) )
            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));

        if ( preg_match(Exception::NullDereferenceRegex, $errstr) )
            exceptionHandler(new NullReferenceException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UndefinedPropertyRegex, $errstr) )
            exceptionHandler(new UndefinedPropertyException($errstr, null, $errfile, $errline));

        if( preg_match(Exception::UndefinedConstantRegex, $errstr) )
            exceptionHandler(new UndefinedConstantException($errstr, null, $errfile, $errline));
    }

    print("UNHANDLED ERROR: " . FriendlyErrorType($errno) ."- $errstr file $errfile on line $errline \n");
    die($errno);
}

function exceptionHandler(\Exception $exception) {

    if( $exception instanceOf SemanticException ) {
        print $exception . PHP_EOL;
    }
    elseif( $exception instanceOf Exception ) {
        print $exception . PHP_EOL;
        print $exception->stackTrace . PHP_EOL;
    }
    else {
        print $exception . PHP_EOL;
    }

    exit($exception->code);
}

function fatalErrorHandler() {
    try {
        # Getting last error
        $e = error_get_last();

        if( isset($e['type']) ) {
            if ($e['type'] & (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR) ) {
                ob_clean();
                $code = $e['type'];
                $msg  = $e['message'];
                $file = $e['file'];
                $line = $e['line'];
                errorHandler($code,$msg,$file,$line,null);
            }
        }
    } catch( Exception $e ) {
        exceptionHandler($e);
    }
}
?>