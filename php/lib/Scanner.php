<?php
namespace AppChecker;
use AppChecker\Scanner;
use AppChecker\Utils;

class Scanner {
	public $scanners = [];
	public $classCached = [];

	public function __construct() {
		foreach (Utils::ScanDirectory(dirname(__FILE__) . '/scanner/', false, 'getBaseName') as $index => $value) {
			array_push($this->scanners, 'AppChecker\\Scanner\\' . str_replace('.php', '', $value));
		}
	}
	public function Run() {
		foreach ($this->scanners as $index => $class) {
			if (!isset($this->classCached[$class])) {
				$this->classCached[$class] = new $class;
			}
			$this->classCached[$class]->Run();
		}
	}
}
