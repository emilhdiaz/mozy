<?php

function in() {

}

function out( $object ) {
    global $process;

    $process->out->writeLine($object);
}

function debug( $string ) {
#    fwrite(STDERR, $string . PHP_EOL . PHP_EOL);
}

function registerClassPath( $classPath ) {
    set_include_path(get_include_path() . PATH_SEPARATOR . $classPath);
}

function convert( $output ) {
    global $framework;

    $format = (
    	$framework->overrideFormat ?
    	$framework->overrideFormat :
    	(
    		$framework->currentRequest ?
    		$framework->currentRequest->format :
    		null
    	)
    );


    debug($format);

    /* Transform the object to a string based on the request format */
    switch($format) {
        case 'string':
            return _S($output, false);
            break;

        case 'serial':
            return serialize($output);
            break;

        case 'native':
            return var_export($output, true);
            break;

        case 'console':
            return $output->__toText();

        default:
            return _S($output);
            break;
    }

    return trim($output);
}

function create_new_class($class, $base = null) {
    // clean inputs
    $class = preg_replace("/[^A-Za-z0-9_\\\]/","", $class);
    $base = preg_replace("/[^A-Za-z0-9_\\\]/","", $base);

    // isolate namespace
    $namespace = substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));

    // remove leading namespace
    $class = str_replace($namespace . NAMESPACE_SEPARATOR, '', $class);
    $base = str_replace($namespace . NAMESPACE_SEPARATOR, '', $base);

    // convert remaining namespace to underscore
    $class = str_replace(NAMESPACE_SEPARATOR, '_', $class);

    $definition =
        "namespace " . $namespace . ";" . PHP_EOL .
        "class " . $class . ($base ? " extends \\" . $base : '') . " {}" . PHP_EOL
    ;

    eval($definition);
    return $class;
}

function camelCase($string) {
    $string = preg_replace('/[_-]/', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    $string = lcfirst($string);
    return $string;
}

function get_class_from_filename($fileName) {
	$className = str_replace(ROOT, '', $fileName);
    $className = str_replace(DIRECTORY_SEPARATOR, NAMESPACE_SEPARATOR, $className);
    $className = substr($className, 0, strrpos($className, '.'));
    $className = trim($className);

    #TODO: Find a better way to handle these special namespace subdirectories
    $className = str_replace('_Exceptions\\', '', $className);
    $className = str_replace('_Interfaces\\', '', $className);
    $className = str_replace('_Tests\\', '', $className);
    $className = str_replace('_Traits\\', '', $className);

    $nameParts = explode(NAMESPACE_SEPARATOR, $className);
    $shortClassName = array_pop($nameParts);

    if( !preg_match('/^[A-Z]/', $shortClassName) )
    	return false;

    return $className;
}

function get_path_namespace($namespace) {
    $path = str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $namespace);
    $path = ROOT . $path;
    return $path;
}

function get_namespace_path($class) {
    $namespace = get_namespace($class);
    $path = ROOT . str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $namespace);
    return $path;
}

function get_namespace($class) {
    return substr($class, (int) 0, strrpos($class, NAMESPACE_SEPARATOR));
}

function get_calling_frame() {
    $trace = debug_backtrace();
    return Mozy\Core\StackFrame::construct($trace[1]);
}

function get_calling_class() {
    $trace = debug_backtrace();
    return get_class_from_filename($trace[1]['file']);
}

function FriendlyErrorType($type) {
    $return ="";
    if($type & E_ERROR) // 1 //
        $return.='& E_ERROR ';
    if($type & E_WARNING) // 2 //
        $return.='& E_WARNING ';
    if($type & E_PARSE) // 4 //
        $return.='& E_PARSE ';
    if($type & E_NOTICE) // 8 //
        $return.='& E_NOTICE ';
    if($type & E_CORE_ERROR) // 16 //
        $return.='& E_CORE_ERROR ';
    if($type & E_CORE_WARNING) // 32 //
        $return.='& E_CORE_WARNING ';
    if($type & E_COMPILE_ERROR) // 64 //
        $return.='& E_COMPILE_ERROR ';
    if($type & E_COMPILE_WARNING) // 128 //
        $return.='& E_COMPILE_WARNING ';
    if($type & E_USER_ERROR) // 256 //
        $return.='& E_USER_ERROR ';
    if($type & E_USER_WARNING) // 512 //
        $return.='& E_USER_WARNING ';
    if($type & E_USER_NOTICE) // 1024 //
        $return.='& E_USER_NOTICE ';
    if($type & E_STRICT) // 2048 //
        $return.='& E_STRICT ';
    if($type & E_RECOVERABLE_ERROR) // 4096 //
        $return.='& E_RECOVERABLE_ERROR ';
    if($type & E_DEPRECATED) // 8192 //
        $return.='& E_DEPRECATED ';
    if($type & E_USER_DEPRECATED) // 16384 //
        $return.='& E_USER_DEPRECATED ';
    return substr($return,2);
}

/**
 * Substitute a null value for a default value
 */
function nvl () {
}

/**
 * Case insensitive key search. Returns value if found NULL otherwise.
 */
function array_value( array $array, $key ) {
    if( empty($array) )
        return null;

    $key = strtolower($key);
    $array = array_change_key_case($array, CASE_LOWER);
    return array_key_exists($key, $array) ? $array[$key] : null;
}

function implode_assoc( array $array, $glue = '', $prefix = '', $postfix = '' ) {
    $string = '';
    foreach($array as $key=>$value) {
        // check if string key
        $string .= (is_string($key) ? $prefix . $key . $postfix : '');

        // check if value is array
        $string .= (is_array($value) ? implode_assoc($value, $glue) :  "'". $value . "'");

        $string .= ' ';
    }
    return $string;
}

/**
 * Returns value if non empty, otherwise $default if set, otherwise null
 */
function clean($value, $default = null) {
    if( !empty($value) )
        return $value;
    elseif( isset($default) )
        return $default;
    else
        return null;
}

#TODO: Sample casting
/**
 * Cast to Array
 */
function _A( $object = [] ) {
    if( is_array($object) )
        return $object;

    if( is_scalar($object) || is_resource($object) )
        return [$object];

    #TODO: implement casting and use __call to check for unsupported casts base on __to* method calls
    if( is_object($object) ) {
        if( method_exists($object, '__toArray()') )
            return $object->__toArray();
        else
            return [$object];
    }

    if( is_null($object) )
        return [];
}

/**
 * Cast to String
 * Arrays represented in (a1, a2, a3, ..., an) notation
 */
function _S( $object = null, $shorten = true ) {
    if( is_string($object) )
        return $object;

    if( is_array($object) ) {
        if( empty($object) ) {
            return '()';
        }
        else {
            foreach( $object as $key=>$value ) {
#                if( is_array($value) )
                    $object[$key] = _S($value);

#                if( is_object($value) )
#                    $object[$key] = get_class($value);

#                if( is_string($value) && $shorten && strlen($value) > 15 )
#                    $object[$key] = substr($value, 0, 15) . '...';
            }
        }
        if( !$shorten ) {
        	$string = 'Array(' . PHP_EOL;
        	foreach( $object as $key=>$value) {
				$string .= " [$key] => " . $value . PHP_EOL;
        	}
        	$string .= ')' . PHP_EOL;
        	return $string;
        }
        else
        	return '('. implode(', ',$object) .')';
    }

    if( is_float($object) )
        return number_format($object, 1);

    if( is_int($object) )
        return strval($object);

    if( is_object($object) )
        return $object->__toString();

    if( is_null($object) )
        return '<null>';

    if( $object === true )
        return '<true>';

    if( $object === false )
        return '<false>';

    if( is_resource($object) )
        return "<resource:$object>";
}

?>