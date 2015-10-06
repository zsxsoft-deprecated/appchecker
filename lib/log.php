<?php

namespace AppChecker\Log;
/**
 * Output error then exit
 * @param string $text
 * @param bool $exit
 */
function Error($text, $exit = true) {
	if ($exit) {
		End($text, 1);
	} else {
		Log($text, "1;31");
	}

}

/**
 * Output warning
 * @param string $text
 * @param bool $exit
 */
function Warning($text, $exit = false) {

	if ($exit) {
		End($text, 1);
	} else {
		Log($text, "1;33");
	}

}

/**
 * Output title
 * @param string $text
 */
function Title($text) {
	$boundary = "===================================================================";
	echo $boundary . PHP_EOL;
	echo str_repeat(" ", (strlen($boundary) - strlen($text)) / 2);
	echo $text . PHP_EOL;
	echo $boundary . PHP_EOL;
}
/**
 * Log
 * @param string $text
 */
function Log($text, $color = "") {
	$text = "[" . date("Y/m/d h:i:s a") . "] " . $text;
	if (PHP_SYSTEM === SYSTEM_WINDOWS) {
		$text = iconv("UTF-8", "gbk", $text);
		if (!(false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI') || 'xterm' === getenv('TERM'))) {
			$color = "";
		}
	}
	if ($color != "") {
		$text = "\033[" . $color . "m" . $text . "\033[0m";
	}

	echo $text . PHP_EOL;

}

/**
 * Output something then exit
 * @param string $text
 */
function End($text, $errno = 0) {
	Log($text);
	exit($errno);
}
