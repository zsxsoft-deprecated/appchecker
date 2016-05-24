<?php
namespace AppChecker\Scanner;
use AppChecker\Log as Log;
use AppChecker\Utils;

$file = "";
$path = "";

class Template {
/**
 * Validate W3C
 */
	public static function ValidateW3C($url) {
		Log::Log('Testing ' . $url);
		ob_flush();
		$validator = new \W3C\HtmlValidator();
		$result = $validator->validateHTML5(file_get_contents($url));

		if ($result->isValid()) {
			Log::Info('Validation successful');
		} else {
			foreach ($result->getErrors() as $error) {
				self::DisplayErrors($error, 'Error');
			}
			foreach ($result->getWarnings() as $warning) {
				self::DisplayErrors($warning, 'Warning');
			}
			Log::Warning('Validation failed: ' . $result->getErrorCount() . " error(s) and " . $result->getWarningCount() . ' warning(s).');
		}
	}
/**
 * Check Useless jQuery
 */
	public static function CheckUselessJQuery() {
		global $file;
		global $path;
		$regex = "/src=[\"'](((?!zb_system).)*?jquery[\.0-9\-]*?(min)?\.js)[\"']/i";
		$matches = null;
		if (preg_match($regex, $file, $matches)) {
			Log::Error('Detected useless jQuery: ' . $matches[1] . ' in ' . $path);
		}
	}

	public static function DisplayErrors($object, $type) {
		$function = ucfirst($type);
		Log::$function('In Line ' . $object->getLine() . ', Col ' . $object->getColumn() . ", " . str_replace("\n", "", $object->getMessage()), false);
		Log::Echo ($object->getSource());
		Log::Line();
	}

	public static function CheckW3C() {
		global $zbp;
		global $app;
		Log::Log("Checking W3C...");
		//$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] = true;
		//if (!$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE']) {
		//Log::Warning("You should permanentize your domain to validate.");
		//return;
		//}

		Log::Log("Changing Theme...");
		// Change Theme
		$origTheme = $zbp->option['ZC_BLOG_THEME'];
		$origCSS = $zbp->option['ZC_BLOG_CSS'];
		$zbp->Config('system')->ZC_BLOG_THEME = $app->id;
		$zbp->Config('system')->ZC_BLOG_CSS = array_keys($app->GetCssFiles())[0];
		$zbp->SaveConfig('system');

		Log::Log("Validating INDEX");
		self::ValidateW3C($zbp->host);
		//Log::Log("Validating ?id=1");
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
	public static function RunChecker($filePath) {
		global $file;
		global $path;
		$path = $filePath;
		$file = file_get_contents($path);
		self::CheckUselessJQuery();
	}
/**
 * Run
 */
	public static function Run() {
		global $zbp;
		global $app;

		if ($app->type == 'plugin') {
			return;
		}

		Log::Title('THEME STANDARD');
		// Log::Log('Scanning useless jQuery');
		$templateDir = $zbp->path . 'zb_users/theme/' . $app->id . '/template/';
		foreach (Utils::ScanDirectory($templateDir) as $index => $value) {
			self::RunChecker($value);
		}

		self::CheckW3C();
	}

}