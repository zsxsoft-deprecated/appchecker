<?php
namespace AppChecker\Scanner\Template;
$file = "";
$path = "";

/**
 * Validate W3C
 */
function ValidateW3C($url) {
	\AppChecker\Log\Log('Testing ' . $url);
	ob_flush();
	$validator = new \W3C\HtmlValidator();
	$result = $validator->validateHTML5(file_get_contents($url));

	if ($result->isValid()) {
		\AppChecker\Log\Info('Validation successful');
	} else {
		foreach ($result->getErrors() as $error) {
			DisplayErrors($error, 'Error');
		}
		foreach ($result->getWarnings() as $warning) {
			DisplayErrors($warning, 'Warning');
		}
		\AppChecker\Log\Warning('Validation failed: ' . $result->getErrorCount() . " error(s) and " . $result->getWarningCount() . ' warning(s).');
	}
}
/**
 * Check Useless jQuery
 */
function CheckUselessJQuery() {
	global $file;
	global $path;
	$regex = "/src=[\"']?(((?!zb_system).)*?jquery[\.0-9\-]*?(min)?\.js)[\"']?/i";
	$matches = null;
	return;
	if (preg_match($regex, $file, $matches)) {
		\AppChecker\Log\Error('Detected useless jQuery: ' . $matches[1] . ' in ' . $path);
	}
}

function DisplayErrors($object, $type) {
	$function = "\AppChecker\\Log\\" . ucfirst($type);
	$function('In Line ' . $object->getLine() . ', Col ' . $object->getColumn() . ", " . str_replace("\n", "", $object->getMessage()), false);
}

function CheckW3C() {
	global $zbp;
	global $app;
	\AppChecker\Log\Log("Checking W3C...");
	if (!$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']) {
		\AppChecker\Log\Warning("You should permanentize your domain to validate.");
		return;
	}

	\AppChecker\Log\Log("Changing Theme...");
	// Change Theme
	$origTheme = $zbp->option['ZC_BLOG_THEME'];
	$origCSS = $zbp->option['ZC_BLOG_CSS'];
	$zbp->Config('system')->ZC_BLOG_THEME = $app->id;
	$zbp->Config('system')->ZC_BLOG_CSS = array_keys($app->GetCssFiles())[0];
	$zbp->SaveConfig('system');

	\AppChecker\Log\Log("Validating INDEX");
	ValidateW3C($zbp->host);
	//\AppChecker\Log\Log("Validating ?id=1");
	//ValidateW3C(str_replace('//?', '/?', $zbp->host . "/?id=1"));

	// Revert Theme
	$zbp->Config('system')->ZC_BLOG_THEME = $origTheme;
	$zbp->Config('system')->ZC_BLOG_CSS = $origCSS;
	$zbp->SaveConfig('system');
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

	CheckW3C();
}

return __NAMESPACE__;
