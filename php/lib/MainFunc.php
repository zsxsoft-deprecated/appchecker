<?php
namespace AppChecker;

use AppChecker\Log;
use AppChecker\Scanner;

class MainFunc
{

    public static function testApp($appId)
    {

        global $zbp;
        global $app;
        global $config;
        global $bloghost;
        $bloghost = &$config->WebsiteUrl;

        if ($bloghost == "") {
            $bloghost = "http://localhost/";
        }
        $zbp->option['ZC_BLOG_HOST'] = $bloghost;
        $zbp->host = $bloghost;
        

        Log::log('Detected $bloghost = ' . $bloghost);
        Log::info('Completed!');
        Log::log('Getting App...');
        

        if ($zbp->CheckApp($appId)) {
            Log::error('You should disable ' . $appId . ' in Z-BlogPHP first.');
        }
        
        $app = $zbp->LoadApp('plugin', $appId);
        if ($app->id !== null) {
            Log::info('Detected Plugin.');
        } else {
            $app = $zbp->LoadApp('theme', $appId);
            if ($app->id !== null) {
                Log::info('Detected Theme.');
            } else {
                Log::error('App not Found!');
            }
        }

        Log::Title("System Information");
        Log::info("Z-BlogPHP: " . ZC_VERSION_FULL);
        Log::info("System: " . \GetEnvironment());

        $scanner = new Scanner();
        $scanner->run();
        Log::info('OK!');
    }

    public static function installApp($filePath)
    {
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
