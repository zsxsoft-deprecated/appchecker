<?php
namespace AppChecker;
class Scanner {
	public static $scanners = [];
	public static function Run() {
		foreach (self::$scanners as $index => $class) {
			$class::Run();
		}
	}
}

foreach (\AppChecker\Utils\ScanDirectory(dirname(__FILE__) . '/scanner/', false, 'getBasename') as $index => $value) {
	array_push(Scanner::$scanners, str_replace('.php', '', $value));
}