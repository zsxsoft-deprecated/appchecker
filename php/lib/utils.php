<?php
namespace AppChecker\Utils;
/**
 * Include file
 * @param string $path
 */
function IncludeFile($path) {
	global $zbp;
	if (is_readable($path)) {
		require $path;
		return true;
	} else {
		return false;
	}
}

function ScanDirectory($path) {
	$ret = [];
	$dir = new \RecursiveDirectoryIterator($path);
	$iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

	foreach ($iterator as $name => $object) {
		if (!$object->isDir()) {
			array_push($ret, $object->getPathName());
		}
	}

	return $ret;
}