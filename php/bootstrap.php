<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/error
 * @php >= 5.3
 */
namespace AppChecker;
use AppChecker\Install;
use AppChecker\Log;
use AppChecker\Run;
use Symfony\Component\Console\Application;
require './lib/utils.php';
foreach (\AppChecker\Utils\ScanDirectory(dirname(__FILE__) . '/lib/', false) as $index => $value) {
	require_once $value;
}

$path = getenv('ZBP_PATH');
if (!is_dir($path) || !chdir($path)) {
	echo 'Cannot open your Z-BlogPHP index.php: ' . $path;
	exit;
}
require 'zb_system/function/c_system_base.php';

Log::Log('Loading Z-BlogPHP...');
$zbp->Load();
\ZBlogException::ClearErrorHook();

$application = new Application();
$application->add(new Run());
$application->add(new Install());
$application->run();
