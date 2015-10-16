<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/error
 * @php >= 5.3
 */
namespace AppChecker;

foreach (['utils', 'log', 'scanner'] as $index => $item) {
	require 'lib/' . $item . '.php';
}
$config = json_decode(file_get_contents('config.json'));
if (!chdir($config->path)) {
	Log\Error('Cannot open ' . $config->path);
}
require 'zb_system/function/c_system_base.php';
\ZBlogException::ClearErrorHook();
Log\Log('Loading Z-BlogPHP...');
$zbp->Load();
Log\Log('Completed!');
Log\Log('Getting App...');
if (count($argv) == 1) {
	Log\Error('No App ID!');
}
$appId = $argv[1];
if ($zbp->CheckApp($appId)) {
	Log\Error('You should disable ' . $appId . ' in Z-BlogPHP first.');
}
$app = $zbp->LoadApp('plugin', $appId);
if ($app->id !== null) {
	Log\Log('Detected Plugin.');
} else {
	$app = $zbp->LoadApp('theme', $appId);
	if ($app->id !== null) {
		Log\Log('Detected Theme.');
	} else {
		Log\Error('App not Found!');
	}
}
Scanner\Run();
Log\Log('OK!');