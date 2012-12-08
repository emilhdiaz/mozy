<?php
namespace Mozy\Core\System;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class ConsoleOutput extends Object implements Singleton {
    protected $items = [];
    protected $overrides = [];

    private static $attribute = [
        0           => '00',
        'bold'      => '01',
        'underscore'=> '04',
        'blink'     => '05',
        'reverse'   => '07',
        'concealed' => '08',
    ];

    private static $foreground = [
        0           => '37',
        'black'     => '30',
        'red'       => '31',
        'green'     => '32',
        'yellow'    => '33',
        'blue'      => '34',
        'magenta'   => '35',
        'cyan'      => '36',
        'white'     => '37',
    ];

    private static $background = [
        0           => false,
        'black'     => '40',
        'red'       => '41',
        'green'     => '42',
        'yellow'    => '43',
        'blue'      => '44',
        'magenta'   => '45',
        'cyan'      => '46',
        'white'     => '47',
    ];

    private function addItem($text = '', $nl = false, $attribute = 0, $foreground = 0, $background = 0, $ignoreOverrides = false ) {
        $this->items[] = [
            'text'      => ($text ? _S($text) : ''),
            'nl'        => $nl,
            'attribute' => $attribute,
            'foreground'=> $foreground,
            'background'=> $background,
            'modifier'  => $this->modifier($attribute, $foreground, $background),
            'ignoreOverrides' => $ignoreOverrides
        ];

        return $this;
    }

    private function modifier($attribute = 0, $foreground = 0, $background = 0) {
        $modifier = '';

        if( $attribute || $foreground || $background ) {
            $modifier = "\033[";
            if( $attribute )
                $modifier .= self::$attribute[$attribute ?: 0] . (($foreground || $background) ? ";" : '');
            if( $foreground )
                $modifier .= self::$foreground[$foreground ?: 0] . ($background ? ";" : '');
            if( $background )
                $modifier .= self::$background[$background ?: 0];
            $modifier .= "m";
        }

        return $modifier;
    }

    public function text( $text, $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false ) {
        return $this->addItem($text, false, $attribute, $color, $background, $ignoreOverrides);
    }

    public function line( $text, $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false ) {
        return $this->addItem($text, true, $attribute, $color, $background, $ignoreOverrides);
    }

    public function nl() {
        return $this->addItem(null, true);
    }

    public function each( $array, $prefix = '', $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false  ) {
        foreach($array as $key=>$value) {
            $text = $prefix . $key . ': ' . $value;
            $this->addItem($text, true, $attribute, $color, $background, $ignoreOverrides);
        }
        return $this;
    }

    public function overrides( $text, $attribute = 0, $foreground = 0, $background = 0 ) {
        $this->overrides[] = [
            'text'      => $text,
            'nl'        => false,
            'attribute' => $attribute,
            'foreground'=> $foreground,
            'background'=> $background,
            'modifier'  => $this->modifier($attribute, $foreground, $background)
        ];
    }

    public function __toString() {
        $output = '';

        foreach($this->items as $item) {
            extract($item);
            if( !$ignoreOverrides ) {
                foreach($this->overrides as $override) {
                    $replacement = $override['modifier'] . $override['text'] . $modifier;
                    $text = str_replace($override['text'], $replacement, $text);
                }
            }
            $output .= ($text ? $modifier . $text : '') . ($nl ? PHP_EOL : '');
        }
        $this->flush();
        return $output;
    }

    public function flush() {
        $this->items = [];
        $this->overrides = [];
    }
}
?>