<?php
namespace Mozy\Core;

/**
 * Color escapes for bash output
 */
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

    private function addItem($text = '', $nl = false, $attribute = 0, $foreground = 0, $background = 0 ) {
        $this->items[] = [
            'text'      => $text,
            'nl'        => $nl,
            'attribute' => self::$attribute[$attribute ?: 0],
            'foreground'=> self::$foreground[$foreground ?: 0],
            'background'=> self::$background[$background ?: 0]
        ];

        return $this;
    }

    private function buildItem(array $item) {
        $output = '';
        extract($item);

        if( $attribute || $foreground || $background ) {
            $output .= "\033[";
            if( $attribute)
                $output .= $attribute . (($foreground || $background) ? ";" : '');
            if( $foreground )
                $output .= $foreground . ($background ? ";" : '');
            if( $background )
                $output .= $background;
            $output .= "m";
        }
        $output .= $text ? _S($text) : '' . ($nl ? "\n" : '');
        //. "\033[0m";
        return $output;
    }

    public function send() {
        print $this->getOutput();
    }

    public function getOutput() {
        $output = '';
        foreach($this->items as $item) {
            $output .= $this->buildItem($item);
        }
        foreach($this->overrides as $override) {
            $output = str_replace($override['text'], $this->buildItem($override), $output);
        }
        return $output;
    }

    public function text( $text, $attribute = 0, $color = 0, $background = 0 ) {
        return $this->addItem($text, false, $attribute, $color, $background);
    }

    public function line( $text, $attribute = 0, $color = 0, $background = 0 ) {
        return $this->addItem($text, true, $attribute, $color, $background);
    }

    public function nl() {
        return $this->addItem(null, true);
    }

    public function each( $array, $prefix = '', $attribute = 0, $color = 0, $background = 0  ) {
        foreach($array as $key=>$value) {
            $text = $prefix . $key . ': ' . $value;
            $this->addItem($text, true, $attribute, $color, $background);
        }
        return $this;
    }

    public function overrides( $text, $attribute = 0, $foreground = 0, $background = 0 ) {
        $this->overrides[] = [
            'text'      => $text,
            'nl'        => false,
            'attribute' => self::$attribute[$attribute ?: 0],
            'foreground'=> self::$foreground[$foreground ?: 0],
            'background'=> self::$background[$background ?: 0]
        ];
    }
}
?>