<?php
namespace AppChecker\Scanner;
use AppChecker\Log as Log;
use AppChecker\Utils as Utils;

class GlobalVariables {

	private $store = [];
	/**
	 * Load globals and save them to the store
	 * @param string   $class
	 * @param callable $callback
	 */
	public function LoadGlobals($class, callable $callback) {
		$this->store[$class] = [
			"callback" => $callback,
			"data" => $callback(),
		];
	}

	/**
	 * Compare new data and original data
	 * @param string   $class
	 */
	public function DiffGlobals($class) {
		return array_diff($this->store[$class]['callback'](), $this->store[$class]['data']);
	}

	/**
	 * Check the name of functions
	 * @param string   $class
	 */
	public function CheckFunctions($diff) {
		global $app;
		Log::Log('Testing functions');
		$regex = str_replace("!!", $app->id, "/^(activeplugin_|installplugin_|uninstallplugin_)!!$|^!!_|^!!$/si");
		//var_dump($diff);exit;
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
	public function CheckOthers($class, $diff) {
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
	public function CheckDiff($class) {
		$diff = $this->DiffGlobals($class);
		$function = 'Check' . ucfirst($class);
		if (method_exists(__CLASS__, $function)) {
			return call_user_func(array(__CLASS__, $function), $diff);
		}
		return $this->CheckOthers($class, $diff);
	}

/**
 * Runner
 */
	public function Run() {
		global $zbp;
		global $app;

		Log::Title('GLOBAL VARIABLES');
		Log::Log('Scanning functions and global variables');
		$this->LoadGlobals('variables', function () {
			return array_keys($GLOBALS);
		});
		$this->LoadGlobals('functions', function () {
			return get_defined_functions()['user'];
		});
		$this->LoadGlobals('constants', function () {
			return array_keys(get_defined_constants());
		});
		$this->LoadGlobals('classes__', function () {
			return get_declared_classes();
		});
		$filename = $zbp->path . '/zb_users/' . $app->type . '/' . $app->id . '/include.php';
		if (!Utils::includeFile($filename)) {
			Log::Log('No include file.');
			return;
		}

		$this->CheckDiff('variables');
		$this->CheckDiff('functions');
		$this->CheckDiff('constants');
		$this->CheckDiff('classes__');

	}

}