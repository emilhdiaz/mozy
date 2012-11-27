<?php
namespace Mozy\Core;

use Mozy\Core\Reflection\ReflectionClass;

require_once('common.php');
require_once('_Interfaces/Singleton.php');
require_once('_Traits/ApplicationContext.php');
require_once('Reflection/_Interfaces/Documented.php');
require_once('Reflection/ReflectionMethod.php');
require_once('Reflection/ReflectionClass.php');
require_once('Object.php');
require_once('Factory.php');

define('ROOT', getcwd().'/php');

const Object        = 'Mozy\Core\Object';
const Immutable     = 'Mozy\Core\Immutable';
const Singleton     = 'Mozy\Core\Singleton';
const Factory       = 'Mozy\Core\Factory';
const TestCase      = 'Mozy\Core\Test\TestCase';
const TestScenario  = 'Mozy\Core\Test\TestScenario';
const Assertion     = 'Mozy\Core\Test\Assertion';

global $framework;

final class Framework extends Object implements Singleton {

    private static $self;
    protected $version = 1.0;
    protected $server;
    protected $console;
    protected $application;
    protected $allowSubprocess = [];

    protected function __construct() {
        // configure Class Path & Autoloaders
        $this->registerClassPath( ROOT );
        spl_autoload_register( [$this,'coreAutoloader'], true );
        $autoloader = AutoLoader::construct();
        $autoloader->registerLoader( ClassLoader::construct() );
        $autoloader->registerLoader( InterfaceLoader::construct() );
        $autoloader->registerLoader( TraitLoader::construct() );
        $autoloader->registerLoader( ExceptionLoader::construct() );
        $autoloader->registerLoader( TestLoader::construct() );

        $this->server = Server::construct();
        $this->console = Console::construct();
    }

    public static function init() {
        global $framework;

        // bootstrap the PHP environment
        self::bootstrap();

        // initialize the framework
        $framework = self::construct();

        // write the PID
        $framework->console->output->line("PHP Process (".getmypid().")")->send();
    }

    public static function bootstrap() {
        // configure Error and Logging Runtime Configurations
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('display_errors', true);                //default false
        ini_set('display_startup_errors', true);        //default false
        ini_set('log_errors', false);                   //default true
        ini_set('log_errors_max_len', 1024);            //default 1024
        ini_set('ignore_repeated_errors', false);       //default false
        ini_set('ignore_repeated_source', false);       //default false
        ini_set('report_memleaks', true);               //default true
        ini_set('track_errors', false);                 //default false
        ini_set('html_errors', false);                  //default false
        ini_set('xmlrpc_errors', false);                //default false

        // configure Error and Exception Handlers
        set_error_handler( 'Mozy\Core\errorHandler' );
        set_exception_handler( 'Mozy\Core\exceptionHandler' );
        register_shutdown_function('Mozy\Core\fatalErrorHandler');
        assert_options(ASSERT_WARNING, FALSE);

        /* Buffer Output */
        ob_start();
    }

    protected function coreAutoloader($className) {
        $this->registerClassPath( ROOT . '/Mozy/Core');
        $this->registerClassPath( ROOT . '/Mozy/Core/_Exceptions');
        $this->registerClassPath( ROOT . '/Mozy/Core/_Interfaces');
        $this->registerClassPath( ROOT . '/Mozy/Core/_Tests');
        $this->registerClassPath( ROOT . '/Mozy/Core/Autoloaders');
        $this->registerClassPath( ROOT . '/Mozy/Core/Reflection');
        $this->registerClassPath( ROOT . '/Mozy/Core/Reflection/_Exceptions');
        $this->registerClassPath( ROOT . '/Mozy/Core/Reflection/_Interfaces');

        $nameParts = explode('\\', $className);
        $shortClassName = array_pop($nameParts);
        $fullFilePath = stream_resolve_include_path($shortClassName . '.php');

        if( $fullFilePath ) {
            include_once($fullFilePath);
            if( class_exists($className) && method_exists($className, 'bootstrap') )
                $className::bootstrap();
        }
    }

    public function processExchange() {
        switch( $this->gateway ) {
            case 'CLI':
                $request = ConsoleRequest::current();
                break;

            case 'CGI':
                break;
        }

        $this->allowSubprocess = (bool) (array_value($request->arguments, 'allowSubprocess') === true);

        $this->executeTarget($request->api, $request->action, $request->arguments, $request->format);
    }

    public function registerClassPath( $classPath ) {
        set_include_path(get_include_path() . PATH_SEPARATOR . $classPath);
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

    public function executeTarget($api, $action, $arguments, $format = null, $separateProcess = false) {
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

        $method = $api->class->getMethod($action);
        $parameters = [];

        foreach( $method->getParameters() as $parameter ) {
            #TODO check argument types
            $value = array_value($arguments, $parameter->name) ?: array_value($arguments, $parameter->getPosition());

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

        if( $separateProcess ) {
            $request = ConsoleRequest::construct($this->endpoint, $api->name, $action, $parameters);
            $response = $request->send();
#            $result = $response->result;
        }
        else {
            $result = $method->invokeArgs($api, $parameters);
        }
    }

    public function printErrorReportingLevels() {
        $this->console->output->line(FriendlyErrorType(error_reporting()))->send();
    }
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
#    Console::println(FriendlyErrorType($errno) ." : $errstr file $errfile on line $errline \n");
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
    die();
}

function exceptionHandler(\Exception $exception) {

    if( $exception instanceOf SemanticException ) {
        print($exception);
    }
    elseif( $exception instanceOf Exception ) {
        print($exception);
        print($exception->getStackTrace());
    }
    else {
        print($exception);
    }

    die();
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