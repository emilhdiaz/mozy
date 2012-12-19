<?php
namespace Mozy\Core\System\Console;

use Mozy\Core\Object;
use Mozy\Core\Singleton;

class ConsoleOutput extends Object implements Singleton {

	protected $lines = [];
    protected $overrides = [];

    private static $attribute = [
        'normal'    => '00',
        'bold'      => '01',
        'faint'		=> '02',
        'italic'	=> '03',
        'underscore'=> '04',
        'blink'     => '05',
        'reverse'   => '07',
        'concealed' => '08',
        'crossed'	=> '09',
    ];

    private static $foreground = [
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
		$items = [];

		/* Do not process overrides. Just add directly */
		if ( $ignoreOverrides ) {
        	$items[] = [
				'text'      => $text,
	           	'attribute' => $attribute,
	            'foreground'=> $foreground,
	            'background'=> $background,
	            'modifier'  => $this->modifier($attribute, $foreground, $background)
        	];
		}
		/* Process overrides and split text into parts. Merge into items. */
		else {
			$items = $this->split($text, $attribute, $foreground, $background);
		}

		/* Add new line */
        if ( $nl ) {
           	$items[] = [
           		'text'		=> PHP_EOL,
				'attribute' => $attribute,
	            'foreground'=> $foreground,
	            'background'=> $background,
           		'modifier'	=> $this->modifier($attribute, $foreground, $background)
           	];
        }

        foreach($items as $item) {
            print $item['modifier'] . $item['text'];
        }

        return $this;
    }

	/**
	 * Recursive algorithm that will split the text on the first pattern
	 * match and continue to split each half until no patterns match
	 */
	private function split($text = '', $attribute = 0, $foreground = 0, $background = 0) {
		if( strlen($text) === 0 )
			return [];

		$found 				= false;
		$override			= null;
		$overrideText 		= null;
		$overrideStart 		= null;
		$overrideEnd		= null;
		$overrideAttribute	= null;
		$overrideForeground	= null;
		$overrideBackground	= null;

		/* Step through each pattern rule */
		foreach( $this->overrides as $override ) {
			if ( preg_match($override['pattern'], $text, $match, PREG_OFFSET_CAPTURE) ) {
				$found = true;

				/* Determine if matching subpattern or entire patter */
				$match = isset($match[1]) ? $match[1] : $match[0];

		        $override['text'] 		= $match[0];
           		$override['attribute'] 	= $override['attribute'] ?: $attribute;
           		$override['foreground'] = $override['foreground'] ?: $foreground;
           		$override['background'] = $override['background'] ?: $background;
           		$override['modifier'] 	= $this->modifier($override['attribute'], $override['foreground'], $override['background']);

				/* Determine positions to split string */
				$overrideStart = $match[1];
				$overrideEnd = $overrideStart + strlen($override['text']);

				/* Found match. Do not search other patterns */
				break;
			}
		}

		/* If not found then no more splitting required */
		if ( !$found ) {
			return [
				[
				'text'      => $text,
	           	'attribute' => $attribute,
	            'foreground'=> $foreground,
	            'background'=> $background,
	            'modifier'  => $this->modifier($attribute, $foreground, $background)
        		]
        	];
		}

		/* Recurse into before and after string */
		return array_merge(
			$this->split( substr($text, 0, $overrideStart), $attribute, $foreground, $background ),
			[$override],
			$this->split( substr($text, $overrideEnd), $attribute, $foreground, $background )
		);
	}

    private function modifier($attribute = 0, $foreground = 0, $background = 0) {
        $modifier = Console::CSI;
        if ( $attribute || $foreground || $background ) {
            if ( $attribute )
                $modifier .= self::$attribute[$attribute] . (($foreground || $background) ? ";" : '');
            if ( $foreground )
                $modifier .= self::$foreground[$foreground] . ($background ? ";" : '');
            if ( $background )
                $modifier .= self::$background[$background];
        }

        $modifier .= "m";

        return $modifier;
    }

    public function text( $text, $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false ) {
        return $this->addItem($text, false, $attribute, $color, $background, $ignoreOverrides);
    }

    public function column( $text, $width, $align = -1, $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false ) {
    	$text = strlen($text) > $width ? substr($text, 0, $width-3) . "..." : $text;
    	$pad  = strlen($text) < $width ? $width - strlen($text) : 0;
    	if ( $align == -1 )
    		$text = $text . str_repeat(" ", $pad);
    	else if ( $align == 0 )
    		$text = str_repeat(" ", floor($pad/2)) . $text . str_repeat(" ", ceil($pad/2));
    	else
    		$text = str_repeat(" ", $pad) . $text;

        return $this->addItem($text, false, $attribute, $color, $background, $ignoreOverrides);
    }

    public function line( $text, $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false ) {
        return $this->addItem($text, true, $attribute, $color, $background, $ignoreOverrides);
    }

    public function nl($attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false) {
        return $this->addItem('', true, $attribute, $color, $background, $ignoreOverrides);
    }

    public function each( $array, $prefix = '', $attribute = 0, $color = 0, $background = 0, $ignoreOverrides = false  ) {
        foreach($array as $key=>$value) {
            $text = $prefix . $key . ': ' . $value;
            $this->addItem($text, true, $attribute, $color, $background, $ignoreOverrides);
        }
        return $this;
    }

    public function rule( $name, $pattern, $attribute = 0, $foreground = 0, $background = 0 ) {
        $this->overrides[] = [
            'name'		=> $name,
            'pattern'	=> $pattern . 'x', //PCRE eXtended modifier (ignore whitespace in pattern)
            'attribute' => $attribute,
            'foreground'=> $foreground,
            'background'=> $background,
        ];
    }

    public function override( $text, $attribute = 0, $foreground = 0, $background = 0 ) {
        $this->overrides[] = [
        	'name'		=> $text,
            'pattern'	=> '/' . preg_quote($text) . '/',
            'attribute' => $attribute,
            'foreground'=> $foreground,
            'background'=> $background,
        ];
    }

	public function clearOverrides() {
		$this->overrides = [];
	}

	#TODO: replace this with a different rule set that can be toggled
    public function enableSourceHighlighting() {
    	/* highlight comments */
    	$this->rule('comments', '/ ((\/\*|\*|\#) .*) /', 'bold', 'black');

		/* highlight control structures */
		$this->rule('control structures', '/ \W (
			if|else|elseif|do|while|foreach|for|break|
			continue|switch|declare|return|require_once|require|
			include_once|include|goto|throw|new|and|or|xor|instanceOf|as|array
		) \W+/', 'bold', 'blue');

		/* highlight variables */
		$this->rule('variables', '/ \$\w+ /', 'normal', 'yellow');

		/* highlight strings */
		$this->rule('strings', '/ (["\'] .* ["\']) /', 'normal', 'green');

		/* highlight function definitions */
		$this->rule('function definition', '/function \s+ (\w+)/', 'underscore', 'cyan');

		/* highlight default constants */
		$this->rule('default constants', '/ \W (false|true|NULL) \W+/', 'bold', 'blue');

		/* highlight OOP definitions */
		$this->rule('oop definitions', '/ \W (class|interface|trait|public|protected|private|function|static|self|parent) \W+/', 'normal', 'cyan');

		/* highlight object access */
		$this->rule('object access', '/ \w+ (\:\: | \-\>) \w+/', 'bold', 'green');

		/* highlight enclosures */
		$this->rule('enclosures', quoted_regex(['{','}']), 'bold', 'white');

		/* highlight parenthesis */
		$this->rule('parenthesis', quoted_regex(['(',')']), 'normal', 'white');

		/* highlight square brackets (short array syntax) */
		$this->rule('square brackets', quoted_regex(['[',']']), 'bold', 'blue');

		/* highlight ternary operators */
		$this->rule('ternary operators', quoted_regex(['?:', ':']), 'bold', 'green');

		/* highlight logical operators */
		$this->rule('logical operators', quoted_regex(['&&', '||']), 'bold', 'yellow');

		/* highlight comparison and bitwise operators */
		$this->rule('comparison and bitwise operators', quoted_regex([
			'!===', '===', '==', '!=', '<>', '<=', '>=', '<<',
			'>>', '<', '>', '!', '&', '|', '^', '~'
		]), 'bold', 'yellow');

		/* highlight incremental / decremental operators */
		$this->rule('incremental / decremental operators', quoted_regex(['++', '--']), 'bold', 'yellow');

		/* highlight arithmetic operators */
		$this->rule('arithmetic operators', quoted_regex(['-','+','*','%','/']), 'bold', 'yellow');

		/* highlight error control and execution operators */
		$this->rule('error control and execution operators', quoted_regex(['@', '`']), 'bold', 'yellow');

		/* highlight assignment operators */
		$this->rule('assignment operators', quoted_regex(['.=', '.', '=']), 'bold', 'yellow');
    }
}
?>