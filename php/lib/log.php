<?php
namespace AppChecker\Log;
$outputInterface = null;

function SetOutputInterface(&$interface) {
	global $outputInterface;
	$outputInterface = $interface;
}

/**
 * Output error then exit
 * @param string $text
 * @param bool $exit
 */
function Error($text, $exit = true) {
	if ($exit) {
		End($text, 1);
	} else {
		Log('<error>' . $text . '</error>');
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
		Log('<question>' . $text . '</question>');
	}

}

/**
 * Output title
 * @param string $text
 */
function Title($text) {
	global $outputInterface;
	$boundary = "===================================================================";
	$outputInterface->writeln($boundary);
	$outputInterface->writeln(str_repeat(" ", (strlen($boundary) - strlen($text)) / 2) . $text);
	$outputInterface->writeln($boundary);
}
/**
 * Log
 * @param string $text
 */
function Log($text) {
	global $outputInterface;

	$text = "[" . date("Y/m/d h:i:s a") . "] " . $text;
	if (defined('PHP_SYSTEM')) {
		if (PHP_SYSTEM === SYSTEM_WINDOWS) {
			$text = iconv("UTF-8", "gbk", $text);
		}
	}

	if (is_null($outputInterface)) {
		echo $text;
	} else {
		$outputInterface->writeln($text);
	}

}

/**
 * Output something then exit
 * @param string $text
 */
function End($text, $errno = 0) {
	Log($text);
	exit($errno);
}
