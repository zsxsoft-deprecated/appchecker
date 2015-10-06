<?php

namespace AppChecker\Log;
/**
 * Output error then exit
 * @param string $text
 */
function Error($text) {
	End("\033[1;31m" . $text . "\033[0m", 1);
}

/**
 * Output warning
 * @param string $text
 */
function Warning($text) {
	Log("\033[1;33m" . $text . "\033[0m", 1);
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
function Log($text) {
	echo "[" . date("Y/m/d h:i:s a") . "] " . $text . PHP_EOL;
}

/**
 * Output something then exit
 * @param string $text
 */
function End($text, $errno = 0) {
	Log($text);
	exit($errno);
}
