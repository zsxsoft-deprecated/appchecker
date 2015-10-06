<?php
namespace AppChecker\Scanner\Plugin;
$file = "";
$path = "";
/**
 * Check CUrl
 */
function CheckCurl() {
	global $file;
	global $path;
	$regex = "/curl_init/i";
	$matches = null;
	if (preg_match($regex, $file, $matches)) {
		\AppChecker\Log\Warning('Maybe using CURL in ' . $path);
		\AppChecker\Log\Warning('Use class Network to replace it.');
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
	CheckCurl();
}
/**
 * Run
 */
function Run() {
	global $zbp;
	global $app;

	\AppChecker\Log\Title('PLUGIN STANDARD');
	// \AppChecker\Log\Log('Scanning useless jQuery');
	$templateDir = $zbp->path . 'zb_users/' . $app->type . '/' . $app->id;
	foreach (\AppChecker\Utils\ScanDirectory($templateDir) as $index => $value) {
		RunChecker($value);
	}
}

return __NAMESPACE__;
