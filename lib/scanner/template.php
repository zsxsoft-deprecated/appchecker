<?php
namespace AppChecker\Scanner\Template;
$file = "";
$path = "";
/**
 * Check Useless jQuery
 */
function CheckUselessJQuery() {
	global $file;
	global $path;
	$regex = "/src=[\"']?(.*?jquery.*?\.js)[\"']?/i";
	$matches = null;
	if (preg_match($regex, $file, $matches)) {
		\AppChecker\Log\Error('Detected useless jQuery: ' . $matches[1] . ' in ' . $path);
	}
}
/**
 * Run Checker
 * @param string $path
 */
function RunChecker($filePath) {
	global $file;
	global $path;
	$path = $filePath;
	$file = file_get_contents($path);
	CheckUselessJQuery();
}
/**
 * Run
 */
function Run() {
	global $zbp;
	global $app;

	if ($app->type == 'plugin') {
		return;
	}

	\AppChecker\Log\Title('THEME STANDARD');
	// \AppChecker\Log\Log('Scanning useless jQuery');
	$templateDir = $zbp->path . 'zb_users/theme/' . $app->id . '/template/';
	foreach (\AppChecker\Utils\ScanDirectory($templateDir) as $index => $value) {
		RunChecker($value);
	}
}

return __NAMESPACE__;
