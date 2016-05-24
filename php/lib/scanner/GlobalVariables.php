<?php
namespace AppChecker\Scanner;
use AppChecker\Log as Log;
use AppChecker\Utils as Utils;

class GlobalVariables {

	private static $store = [];
/**
 * Load globals and save them to the store
 * @param string   $class
 * @param callable $callback
 */
	public static function LoadGlobals($class, callable $callback) {
		self::$store[$class] = [
			"callback" => $callback,
			"data" => $callback(),
		];
	}

/**
 * Compare new data and original data
 * @param string   $class
 */
	public static function DiffGlobals($class) {
		return array_diff(self::$store[$class]['callback'](), self::$store[$class]['data']);
	}

/**
 * Check the name of functions
 * @param string   $class
 */
	public static function CheckFunctions($diff) {
		global $app;
		Log::Log('Testing functions');
		$regex = str_replace("!!", $app->id, "/^(activeplugin_|installplugin_|uninstallplugin_)!!$|^!!_|^!!$/si");
		foreach ($diff as $index => $name) {
			if (preg_match($regex, $name)) {
				Log::Log('Tested function: ' . $name);
			} else {
				Log::Error('Sub-standard function: ' . $name, false);
				if ($ret = Utils::GetFunctionDescription($name)) {
					Log::Error("In " . $ret->getFileName(), false);
					Log::Error("Line " . ($ret->getStartLine() - 1) . " To " . ($ret->getEndLine() - 1), false);
				}
				Log::Error("Exited");
			}
		}
	}

/**
 * Check global variables / constants / class
 * @param string $class
 * @param array  $diff
 */
	public static function CheckOthers($class, $diff) {
		global $app;
		Log::Log('Testing ' . $class);
		$regex = str_replace("!!", $app->id, "/^!!_?/si");
		foreach ($diff as $index => $name) {
			if (preg_match($regex, $name)) {
				Log::Log('Tested ' . $class . ': ' . $name);
			} else {
				Log::Error('Sub-standard ' . $class . ': ' . $name);
			}
		}
	}

/**
 * Call check functions
 * @param string $class
 */
	public static function CheckDiff($class) {
		$diff = self::DiffGlobals($class);
		$function = __NAMESPACE__ . '\\Check' . ucfirst($class);
		if (function_exists($function)) {
			return $function($diff);
		}
		return self::CheckOthers($class, $diff);
	}

/**
 * Runner
 */
	public static function Run() {
		global $zbp;
		global $app;

		Log::Title('GLOBAL VARIABLES');
		Log::Log('Scanning functions and global variables');
		self::LoadGlobals('variables', function () {
			return array_keys($GLOBALS);
		});
		self::LoadGlobals('functions', function () {
			return get_defined_functions()['user'];
		});
		self::LoadGlobals('constants', function () {
			return array_keys(get_defined_constants());
		});
		self::LoadGlobals('classes__', function () {
			return get_declared_classes();
		});
		$filename = $zbp->path . '/zb_users/' . $app->type . '/' . $app->id . '/include.php';
		if (!Utils::includeFile($filename)) {
			Log::Log('No include file.');
			return;
		}

		self::CheckDiff('variables');
		self::CheckDiff('functions');
		self::CheckDiff('constants');
		self::CheckDiff('classes__');

	}

}