<?php
namespace AppChecker\Scanner;
use AppChecker\Log as Log;
use AppChecker\Utils;

class Template {
	private $file = "";
	private $path = "";
	private $forbiddenAsToken = [];

	public function __construct() {
		$fba = &$this->forbiddenAsToken;
		$fba['index'] = [
			'title',
			'articles',
			'pagebar',
			'type',
			'page',
			'date',
			'tag',
			'author',
			'category',
		];
		$fba['single'] = [
			'title',
			'article',
			'type',
			'page',
			'pagebar',
			'comments',
		];
		$fba['comments'] = &$fba['single'];
		$fba['comment'] = &$fba['single'];
	}

	/**
	 * Validate W3C
	 */
	public function ValidateW3C($url) {
		Log::Log('Testing ' . $url);
		ob_flush();
		$validator = new \W3C\HtmlValidator();
		$result = $validator->validateHTML5(file_get_contents($url));

		if ($result->isValid()) {
			Log::Info('Validation successful');
		} else {
			foreach ($result->getErrors() as $error) {
				$this->DisplayErrors($error, 'Error');
			}
			foreach ($result->getWarnings() as $warning) {
				$this->DisplayErrors($warning, 'Warning');
			}
			Log::Warning('Validation failed: ' . $result->getErrorCount() . " error(s) and " . $result->getWarningCount() . ' warning(s).');
		}
	}
	/**
	 * Check Useless jQuery
	 */
	public function CheckUselessJQuery() {
		$regex = "/src=[\"'](((?!zb_system).)*?jquery[\.0-9\-]*?(min)?\.js)[\"']/i";
		$matches = null;
		if (preg_match($regex, $this->file, $matches)) {
			Log::Error('Detected useless jQuery: ' . $matches[1] . ' in ' . $this->path, false);
		}
	}
	/**
	 * Check Error `As`
	 */
	public function CheckAs() {
		$filename = basename($this->path, '.php');
		$regex = '/\\{foreach.+?as(\s+?)\\$(.+?)\s*?\\}/i';
		$path = $this->path;

		if (!isset($this->forbiddenAsToken[$filename])) {
			return;
		}

		if (preg_match_all($regex, $this->file, $matches)) {
			array_walk($matches[2], function ($asName, $key) use ($matches, $path, $filename) {
				if (in_array($asName, $this->forbiddenAsToken[$filename])) {
					Log::Error("You should not use ``$asName`` as the variable for loop!", false);
					Log::Write("In $path");
					Log::Write($matches[0][$key]);
					Log::Error("Exited");
				}
			});
		}
	}
	public function DisplayErrors($object, $type) {
		$function = ucfirst($type);
		Log::$function('In Line ' . $object->getLine() . ', Col ' . $object->getColumn() . ", " . str_replace("\n", "", $object->getMessage()), false);
		Log::Write($object->getSource());
		Log::Line();
	}

	public function CheckW3C() {
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
		$this->ValidateW3C($zbp->host);
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
	public function RunChecker($filePath) {
		$this->path = $filePath;
		$this->file = file_get_contents($this->path);
		$this->CheckUselessJQuery();
		$this->CheckAs();
	}
	/**
	 * Run
	 */
	public function Run() {
		global $zbp;
		global $app;

		if ($app->type == 'plugin') {
			return;
		}

		Log::Title('THEME STANDARD');
		// Log::Log('Scanning useless jQuery');
		$templateDir = $zbp->path . 'zb_users/theme/' . $app->id . '/template/';
		foreach (Utils::ScanDirectory($templateDir) as $index => $value) {
			$this->RunChecker($value);
		}

		$this->CheckW3C();
	}

}