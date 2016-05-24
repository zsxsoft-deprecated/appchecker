<?php
namespace AppChecker;
use AppChecker\Log;
use AppChecker\MainFunc;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command {
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

		Log::SetOutputInterface($output);
		MainFunc::testApp($input->getArgument("appid"), $input->getOption("bloghost"));

	}
}