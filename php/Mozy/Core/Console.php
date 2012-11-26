<?php
namespace Mozy\Core;

/**
 * Color escapes for bash output
 */
abstract class Console extends Object {
    private static $foreground = [
        'black'         => '0;30',
        'dark_gray'     => '1;30',
        'red'           => '0;31',
        'bold_red'      => '1;31',
        'green'         => '0;32',
        'bold_green'    => '1;32',
        'brown'         => '0;33',
        'yellow'        => '1;33',
        'blue'          => '0;34',
        'bold_blue'     => '1;34',
        'purple'        => '0;35',
        'bold_purple'   => '1;35',
        'cyan'          => '0;36',
        'bold_cyan'     => '1;36',
        'white'         => '1;37',
        'bold_gray'     => '0;37',
    ];

    private static $background = [
        'black'         => '40',
        'red'           => '41',
        'magenta'       => '45',
        'yellow'        => '43',
        'green'         => '42',
        'blue'          => '44',
        'cyan'          => '46',
        'light_gray'    => '47',
    ];

    public static function out( $string ) {
        if( is_array($string) )
            print _S($string);
        else
            print $string;
    }

    /**
     * Print with a trailing new line.
     */
    public static function println( $string ) {
        if( is_array($string) )
            print _S($string)."\n";
        else
            print $string."\n";
    }

    /**
     * Recursively prints an array.
     */
    public static function printArray( array $array ) {
        print '<pre>'.print_r($array, true).'</pre>';
    }

    public static function printByLine( array $array, $prefix = '' ) {
        foreach($array as $key=>$value) {
            static::println($prefix . $key . ': ' . $value );
        }
    }

    /**
     * Var_dump
     */
    public static function dump( $object ) {
        var_dump($object);
#        var_export($object);
    }

    public static function inColor($color, $string) {
        return "\033[" . self::$foreground[$color] . "m" . $string . "\033[0m";
    }

    public static function inBackground($color, $string) {
        return "\033[" . self::$background[$color] . 'm' . $string . "\033[0m";
    }
}
?>