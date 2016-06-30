<?php
namespace AppChecker;

use AppChecker\Log;
use AppChecker\Scanner;

class MainFunc {
    public static function testApp($appId, $arguBlogHost) {

        global $zbp;
        global $app;
        global $bloghost;
        $bloghost = &$arguBlogHost;

        if ($bloghost == "") {
            $bloghost = "http://localhost/";
        }
        //$zbp->option['ZC_PERMANENT_DOMAIN_ENABLE'] = false;
        //$zbp->option['ZC_ORIGINAL_BLOG_HOST'] = $zbp->option['ZC_BLOG_HOST'];
        $zbp->option['ZC_BLOG_HOST'] = $bloghost;
        $zbp->host = $bloghost;
        Log::Log('Detected $bloghost = ' . $bloghost);
        Log::Info('Completed!');
        Log::Log('Getting App...');
        if ($zbp->CheckApp($appId)) {
            Log::Error('You should disable ' . $appId . ' in Z-BlogPHP first.');
        }
        $app = $zbp->LoadApp('plugin', $appId);
        if ($app->id !== null) {
            Log::Info('Detected Plugin.');
        } else {
            $app = $zbp->LoadApp('theme', $appId);
            if ($app->id !== null) {
                Log::Info('Detected Theme.');
            } else {
                Log::Error('App not Found!');
            }
        }

        Log::Title("System Information");
        Log::Info("Z-BlogPHP: " . ZC_VERSION_FULL);
        Log::Info("System: " . \GetEnvironment());

        $scanner = new Scanner();
        $scanner->Run();
        Log::Info('OK!');
    }

    public static function installApp($filePath) {
        $xmlData = file_get_contents($filePath);
        $charset = array();
        $charset[1] = substr($xmlData, 0, 1);
        $charset[2] = substr($xmlData, 1, 1);
        if (ord($charset[1]) == 31 && ord($charset[2]) == 139) {
            if (function_exists('gzdecode')) {
                $xmlData = gzdecode($xmlData);
            }
        }

        $appObject = simplexml_load_string($xmlData);
        $appId = $appObject->id;

        return (\App::UnPack($xmlData)) ? $appId : false;
    }
}
