<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/error
 * @php >= 5.3
 */
namespace AppChecker;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Bootstrap extends Command {
	protected function configure() {
		$this
			->setName('run')
			->setDescription('To run checker')
			->addArgument(
				'appid',
				InputArgument::REQUIRED,
				'AppID'
			)
			->addOption(
				'bloghost',
				null,
				InputOption::VALUE_OPTIONAL,
				"Your Z-BlogPHP Url that can use webbrowser to access."
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $scope;
		global $zbp;
		global $app;
		global $blogpath;

		Log\SetOutputInterface($output);

		\ZBlogException::ClearErrorHook();
		Log\Log('Loading Z-BlogPHP...');

		$zbp->Load();
		$bloghost = $input->getOption('bloghost');
		if ($bloghost == "") {
			$bloghost = "http://localhost/";
		}
		$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] = true;
		$zbp->option['ZC_BLOG_HOST'] = $bloghost;

		Log\Log('Detected $bloghost = ' . $bloghost);
		Log\Info('Completed!');
		Log\Log('Getting App...');
		$appId = $input->getArgument('appid');
		if ($zbp->CheckApp($appId)) {
			Log\Error('You should disable ' . $appId . ' in Z-BlogPHP first.');
		}
		$app = $zbp->LoadApp('plugin', $appId);
		if ($app->id !== null) {
			Log\Info('Detected Plugin.');
		} else {
			$app = $zbp->LoadApp('theme', $appId);
			if ($app->id !== null) {
				Log\Info('Detected Theme.');
			} else {
				Log\Error('App not Found!');
			}
		}
		Scanner\Run();
		Log\Info('OK!');
	}
}

foreach (['utils', 'log', 'scanner'] as $index => $item) {
	require 'lib/' . $item . '.php';
}
$path = getenv('ZBP_PATH');
if (!is_dir($path) || !chdir($path)) {
	echo 'Cannot open your Z-BlogPHP index.php: ' . $path;
	exit;
}
require 'zb_system/function/c_system_base.php';

$application = new Application();
$application->add(new Bootstrap());
$application->run();
