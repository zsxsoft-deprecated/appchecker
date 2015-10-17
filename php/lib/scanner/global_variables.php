<?php
namespace AppChecker\Scanner\GlobalVariables;
$store = [];

/**
 * Load globals and save them to the store
 * @param string   $class
 * @param callable $callback
 */
function LoadGlobals($class, callable $callback) {
	global $store;
	$store[$class] = [
		"callback" => $callback,
		"data" => $callback(),
	];
}

/**
 * Compare new data and original data
 * @param string   $class
 */
function DiffGlobals($class) {
	global $store;
	return array_diff($store[$class]['callback'](), $store[$class]['data']);
}

/**
 * Check the name of functions
 * @param string   $class
 */
function CheckFunctions($diff) {
	global $app;
	\AppChecker\Log\Log('Testing functions');
	$regex = str_replace("!!", $app->id, "/^(activeplugin_|installplugin_|uninstallplugin_)!!$|^!!_/si");
	foreach ($diff as $index => $name) {
		if (preg_match($regex, $name)) {
			\AppChecker\Log\Log('Tested function: ' . $name);
		} else {
			\AppChecker\Log\Error('Sub-standard function: ' . $name);
		}
	}
}

/**
 * Check global variables / constants / class
 * @param string $class
 * @param array  $diff
 */
function CheckOthers($class, $diff) {
	global $app;
	\AppChecker\Log\Log('Testing ' . $class);
	$regex = str_replace("!!", $app->id, "/^!!_?/si");
	foreach ($diff as $index => $name) {
		if (preg_match($regex, $name)) {
			\AppChecker\Log\Log('Tested ' . $class . ': ' . $name);
		} else {
			\AppChecker\Log\Error('Sub-standard ' . $class . ': ' . $name);
		}
	}
}

/**
 * Call check functions
 * @param string $class
 */
function CheckDiff($class) {
	$diff = DiffGlobals($class);
	$function = __NAMESPACE__ . '\\Check' . ucfirst($class);
	if (function_exists($function)) {
		return $function($diff);
	}
	return CheckOthers($class, $diff);
}

/**
 * Runner
 */
function Run() {
	global $zbp;
	global $app;

	\AppChecker\Log\Title('GLOBAL VARIABLES');
	\AppChecker\Log\Log('Scanning functions and global variables');
	LoadGlobals('variables', function () {
		return array_keys($GLOBALS);
	});
	LoadGlobals('functions', function () {
		return get_defined_functions()['user'];
	});
	LoadGlobals('constants', function () {
		return array_keys(get_defined_constants());
	});
	LoadGlobals('classes__', function () {
		return get_declared_classes();
	});
	$filename = $zbp->path . '/zb_users/' . $app->type . '/' . $app->id . '/include.php';
	if (!\AppChecker\Utils\includeFile($filename)) {
		\AppChecker\Log\Log('No include file.');
		return;
	}

	CheckDiff('variables');
	CheckDiff('functions');
	CheckDiff('constants');
	CheckDiff('classes__');

}

return __NAMESPACE__;
