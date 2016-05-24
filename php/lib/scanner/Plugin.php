<?php
namespace AppChecker\Scanner;
use AppChecker\Log as Log;

$file = "";
$path = "";
class Plugin {

/**
 * Check CUrl
 */
	public static function CheckUnsafeFunctions() {
		global $file;
		global $path;
		if (!preg_match('/\.php$/i', $path)) {
			return;
		}

		$regex = "/(system|eval|exec)[ \t]*?(\(|\\$|\"|')/i";
		$matches = null;
		if (preg_match($regex, $file, $matches)) {
			Log::Warning('Maybe using unsafe function ' . $matches[1] . ' in ' . $path);
		}
	}

/**
 * Check Order By Rand
 */
	public static function CheckOrderByRand() {
		global $file;
		global $path;
		$regex = "/[\"']rand\(\)[\"'][ \t]*?\=\>[\"'][ \t]*?[\"']|ORDER[ \t]*BY[\t ]*rand\(/i";
		$matches = null;
		if (preg_match($regex, $file)) {
			Log::Warning('Maybe using rand() in MySQL in ' . $path);
			Log::Warning('You should remove it.');
		}
	}
/**
 * Check CUrl
 */
	public static function CheckCurl() {
		global $file;
		global $path;
		$regex = "/curl_init/i";
		$matches = null;
		if (preg_match($regex, $file, $matches)) {
			Log::Warning('Maybe using CURL in ' . $path);
			Log::Warning('Use class Network to replace it.');
		}
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
		self::CheckCurl();
		self::CheckOrderByRand();
		self::CheckUnsafeFunctions();

	}
/**
 * Run
 */
	public static function Run() {
		global $zbp;
		global $app;

		Log::Title('PLUGIN STANDARD');
		// Log::Log('Scanning useless jQuery');
		$templateDir = $zbp->path . 'zb_users/' . $app->type . '/' . $app->id;
		foreach (\AppChecker\Utils::ScanDirectory($templateDir) as $index => $value) {
			self::RunChecker($value);
		}

	}

}