<?php

function quoted_regex( array $patterns ) {
	$quoted = array_map(function ($n) {
		return preg_quote($n, '/');
	}, $patterns);
	return '/' . implode(' | ', $quoted) . '/';
}

function println( $string ) {
	print $string . PHP_EOL;
}

function debug( $string ) {
	if( Mozy\Core\System\Console\Console::$debug ) {
		global $process;
    	$process->err->writeLine($string);
	}
}

function registerClassPath( $classPath ) {
    set_include_path(get_include_path() . PATH_SEPARATOR . $classPath);
}

function create_new_class($class, $base = null) {
    // clean inputs
    $class = preg_replace("/[^A-Za-z0-9_\\\]/","", $class);
    $base = preg_replace("/[^A-Za-z0-9_\\\]/","", $base);

    // isolate class name parts
    $namespace = get_namespace($class);
    $class = str_replace($namespace . NAMESPACE_SEPARATOR, '', $class);

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
	$className = str_replace(MOZY_HOME, '', $fileName);
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

    if ( !preg_match('/^[A-Z]/', $shortClassName) )
    	return false;

    return $className;
}

function get_path_namespace($namespace) {
    $path = str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $namespace);
    $path = MOZY_HOME . $path;
    return $path;
}

function get_namespace($class) {
    return substr($class, 0, strrpos($class, NAMESPACE_SEPARATOR));
}

function get_short_class($class) {
	$pos = strrpos($class, NAMESPACE_SEPARATOR);
	return (($pos === false) and ($class)) ? (NAMESPACE_SEPARATOR . $class) : substr($class, $pos+1);
}

function get_short_file($file) {
	$pos = strrpos($file, DIRECTORY_SEPARATOR);
	return ($pos === false) ? $file : substr($file, $pos+1);
}

function get_calling_class() {
    $trace = debug_backtrace();
    return get_class_from_filename($trace[1]['file']);
}

function FriendlyErrorType($type) {
    $return ="";
    if ($type & E_ERROR) // 1 //
        $return.='& E_ERROR ';
    if ($type & E_WARNING) // 2 //
        $return.='& E_WARNING ';
    if ($type & E_PARSE) // 4 //
        $return.='& E_PARSE ';
    if ($type & E_NOTICE) // 8 //
        $return.='& E_NOTICE ';
    if ($type & E_CORE_ERROR) // 16 //
        $return.='& E_CORE_ERROR ';
    if ($type & E_CORE_WARNING) // 32 //
        $return.='& E_CORE_WARNING ';
    if ($type & E_COMPILE_ERROR) // 64 //
        $return.='& E_COMPILE_ERROR ';
    if ($type & E_COMPILE_WARNING) // 128 //
        $return.='& E_COMPILE_WARNING ';
    if ($type & E_USER_ERROR) // 256 //
        $return.='& E_USER_ERROR ';
    if ($type & E_USER_WARNING) // 512 //
        $return.='& E_USER_WARNING ';
    if ($type & E_USER_NOTICE) // 1024 //
        $return.='& E_USER_NOTICE ';
    if ($type & E_STRICT) // 2048 //
        $return.='& E_STRICT ';
    if ($type & E_RECOVERABLE_ERROR) // 4096 //
        $return.='& E_RECOVERABLE_ERROR ';
    if ($type & E_DEPRECATED) // 8192 //
        $return.='& E_DEPRECATED ';
    if ($type & E_USER_DEPRECATED) // 16384 //
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
    if ( empty($array) )
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
    if ( !empty($value) )
        return $value;
    elseif ( isset($default) )
        return $default;
    else
        return null;
}

#TODO: Sample casting
/**
 * Cast to Array
 */
function _A( $object = [] ) {
    if ( is_array($object) )
        return $object;

    if ( is_scalar($object) || is_resource($object) )
        return [$object];

    #TODO: implement casting and use __call to check for unsupported casts base on __to* method calls
    if ( is_object($object) ) {
        if ( method_exists($object, '__toArray()') )
            return $object->__toArray();
        else
            return [$object];
    }

    if ( is_null($object) )
        return [];
}

/**
 * Cast to String
 * Arrays represented in (a1, a2, a3, ..., an) notation
 */
function _S( $object = null ) {
	if ( in_array( gettype($object), ['boolean', 'integer', 'double', 'NULL']) )
        return var_export($object, true);

    elseif ( is_string($object) )
        return $object;

    elseif ( is_object($object) ) {
		return (string) get_class($object);
    }

    elseif ( is_resource($object) )
        return "RESOURCE:$object";

    elseif ( is_array($object) ) {
		foreach( $object as &$value ) {
			$value = is_string($value) ? quote($value) : _S($value);
		}
       	return '['. implode(', ', $object) .']';
    }
}

function argument_string( $args = null ) {
	$args = _A($args);
	foreach( $args as &$arg ) {
		$arg = is_string($arg) ? quote($arg) : _S($arg);
	}
	return '( ' . implode(', ', $args) . ' )';
}

function quote($string) {
	return "'" . $string . "'";
}

?>
