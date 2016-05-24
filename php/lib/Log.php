<?php
namespace AppChecker;

class Log {

	private static $outputInterface = null;
	/**
	 * SetOutputInterface
	 * @param object &$interface
	 */
	public static function SetOutputInterface(&$interface) {
		self::$outputInterface = $interface;
	}

	/**
	 * Output info
	 * @param string $text
	 * @param bool $exit
	 */
	public static function Info($text) {
		self::Log('<info>' . $text . '</info>');
	}
	/**
	 * Output error then exit
	 * @param string $text
	 * @param bool $exit
	 */
	public static function Error($text, $exit = true) {

		$text = '<error>' . $text . '</error>';
		if ($exit) {
			self::End($text, 1);
		} else {
			self::Log($text);
		}

	}

	/**
	 * Output warning
	 * @param string $text
	 * @param bool $exit
	 */
	public static function Warning($text, $exit = false) {

		$text = '<question>' . $text . '</question>';
		if ($exit) {
			self::End($text, 1);
		} else {
			self::Log($text);
		}

	}

	/**
	 * Output title
	 * @param string $text
	 */
	public static function Title($text) {
		$boundary = "===================================================================";
		self::$outputInterface->writeln($boundary);
		self::$outputInterface->writeln(str_repeat(" ", (strlen($boundary) - strlen($text)) / 2) . $text);
		self::$outputInterface->writeln($boundary);
	}

	/**
	 * Log
	 * @param string $text
	 */
	public static function Line($flush = true) {

		self::Write(PHP_EOL, $flush);

	}
	/**
	 * Log
	 * @param string $text
	 */
	public static function Log($text, $flush = true) {

		$text = "[" . date("Y/m/d h:i:s a") . "] " . $text;
		self::Write($text, $flush);

	}

	/**
	 * Directly Echo
	 */
	public static function Write($text, $flush = true) {

		if (defined('PHP_SYSTEM')) {
			if (PHP_SYSTEM === SYSTEM_WINDOWS && getenv("APPCHECKER_GUI_CHARSET") != "UTF-8") {
				$text = iconv("UTF-8", "gbk", $text);
			}
		}

		if ($flush) {
			ob_flush();
			flush();
		}

		if (is_null(self::$outputInterface)) {
			echo $text . PHP_EOL;
		} else {
			self::$outputInterface->writeln($text);
		}
	}

	/**
	 * Output something then exit
	 * @param string $text
	 */
	public static function End($text, $errno = 0) {
		self::Log($text);
		exit($errno);
	}
}
