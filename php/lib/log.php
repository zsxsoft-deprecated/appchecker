<?php
namespace AppChecker\Log;
$outputInterface = null;

function SetOutputInterface(&$interface) {
	global $outputInterface;
	$outputInterface = $interface;
}

/**
 * Output info
 * @param string $text
 * @param bool $exit
 */
function Info($text) {
	Log('<info>' . $text . '</info>');
}
/**
 * Output error then exit
 * @param string $text
 * @param bool $exit
 */
function Error($text, $exit = true) {

	$text = '<error>' . $text . '</error>';
	if ($exit) {
		End($text, 1);
	} else {
		Log($text);
	}

}

/**
 * Output warning
 * @param string $text
 * @param bool $exit
 */
function Warning($text, $exit = false) {

	$text = '<question>' . $text . '</question>';
	if ($exit) {
		End($text, 1);
	} else {
		Log($text);
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
		if (PHP_SYSTEM === SYSTEM_WINDOWS && getenv("APPCHECKER_GUI_CHARSET") != "GBK") {
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
