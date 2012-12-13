<?php
namespace Mozy\Core;

class SourceParser extends Object implements Singleton, Parser {

	protected $cache = [];

	public function parse( $sourceFile ) {
		if ( !isset($this->cache[$sourceFile]) ) {
			/* Read source file */
			$contents = @file_get_contents( $sourceFile );

			/* Clean up new lines */
#			preg_replace('/(\r\n)|(\n\r)|\r/', PHP_EOL, $contents);
			$contents = str_replace("\r\n", PHP_EOL, $contents);
			$contents = str_replace("\n\r", PHP_EOL, $contents);
			$contents = str_replace("\r", PHP_EOL, $contents);
			$contents = str_replace("\n", PHP_EOL, $contents);
			$contents = str_replace("\t", "    ", $contents);

			/* Break by line */
			$contents = explode(PHP_EOL, $contents);

			/* Adjust keys to match line numbers */
			array_unshift($contents, '');
			unset($contents[0]);

			$this->cache[$sourceFile] = $contents;
		}
		return $this->cache[$sourceFile];
	}

}
?>