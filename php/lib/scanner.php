<?php
namespace AppChecker\Scanner;
$scanners = [];
foreach (\AppChecker\Utils\ScanDirectory(dirname(__FILE__) . '/scanner/') as $index => $value) {
	array_push($scanners, require $value);
}

function Run() {
	global $scanners;
	global $app;
	foreach ($scanners as $index => $function) {
		$function = $function . '\Run';
		$function();
	}
}