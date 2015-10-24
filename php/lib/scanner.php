<?php
namespace AppChecker;
class Scanner {
	public static $scanners = [];
	public static function Run() {
		foreach (self::$scanners as $index => $function) {
			$function = $function . '\Run';
			$function();
		}
	}
}

foreach (\AppChecker\Utils\ScanDirectory(dirname(__FILE__) . '/scanner/') as $index => $value) {
	array_push(Scanner::$scanners, require $value);
}