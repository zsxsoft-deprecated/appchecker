<?php
namespace AppChecker\Scanner;

use AppChecker\Log as Log;

class Plugin
{
    private $file = "";
    private $path = "";
    /**
     * Check unsafe functions
     */
    public function checkUnsafeFunctions()
    {
        if (!preg_match('/\.php$/i', $this->path)) {
            return;
        }

        $regex = "/(system|eval|exec)[ \t]*?(\(|\\$|\"|')/i";
        $matches = null;
        if (preg_match($regex, $this->file, $matches)) {
            Log::warning('Maybe using unsafe function ' . $matches[1] . ' in ' . $this->path);
        }
    }

    /**
     * Check Order By Rand
     */
    public function checkOrderByRand()
    {
        $regex = "/[\"']rand\(\)[\"'][ \t]*?\=\>[\"'][ \t]*?[\"']|ORDER[ \t]*BY[\t ]*rand\(/i";
        $matches = null;
        if (preg_match($regex, $this->file)) {
            Log::warning('Maybe using rand() in MySQL in ' . $this->path);
            Log::warning('You should remove it.');
        }
    }
/**
 * Check CUrl
 */
    public function checkCurl()
    {
        $regex = "/curl_init/i";
        $matches = null;
        if (preg_match($regex, $this->file, $matches)) {
            Log::warning('Maybe using CURL in ' . $this->path);
            Log::warning('Use class Network to replace it.');
        }
    }
/**
 * Run Checker
 * @param string $path
 */
    public function runChecker($filePath)
    {
        $this->path = $filePath;
        $this->file = file_get_contents($this->path);
        $this->checkCurl();
        $this->checkOrderByRand();
        $this->checkUnsafeFunctions();
    }
/**
 * Run
 */
    public function run()
    {
        global $zbp;
        global $app;

        Log::title('PLUGIN STANDARD');
        // Log::Log('Scanning useless jQuery');
        $templateDir = $zbp->path . 'zb_users/' . $app->type . '/' . $app->id;
        foreach (\AppChecker\Utils::ScanDirectory($templateDir) as $index => $value) {
            $this->runChecker($value);
        }
    }
}
