<?php
namespace AppChecker\Scanner;

use AppChecker\Log as Log;
use AppChecker\Utils;
use \W3C\HtmlValidator;

class Template
{
    private $file = "";
    private $path = "";
    private $forbiddenAsToken = [];

    private $origTheme = "";
    private $origCSS = "";

    private $articleUrl = "";

    public function __construct()
    {
        $fba = &$this->forbiddenAsToken;
        $fba['index'] = [
            'title',
            'articles',
            'pagebar',
            'type',
            'page',
            'date',
            'tag',
            'author',
            'category',
        ];
        $fba['single'] = [
            'title',
            'article',
            'type',
            'page',
            'pagebar',
            'comments',
        ];
        $fba['comments'] = &$fba['single'];
        $fba['comment'] = &$fba['single'];
    }

    /**
     * Validate W3C
     * @param string $url
     */
    public function validateW3C($url)
    {
        Log::Log('Testing ' . $url);
        ob_flush();
        $validator = new HtmlValidator();
        $result = $validator->validateHTML5(file_get_contents($url));

        if ($result->isValid()) {
            Log::Info('Validation successful');
        } else {
            foreach ($result->getErrors() as $error) {
                $this->DisplayErrors($error, 'Error');
            }
            foreach ($result->getWarnings() as $warning) {
                $this->DisplayErrors($warning, 'Warning');
            }
            Log::Warning('Validation failed: ' .
                $result->getErrorCount() . " error(s) and " .
                $result->getWarningCount() . ' warning(s).');
        }
    }

    /**
     * Check Error `As`
     */
    public function checkAs()
    {
        $filename = basename($this->path, '.php');
        $regex = '/\\{foreach.+?as(\s+?)\\$(.+?)\s*?\\}/i';
        $path = $this->path;

        if (!isset($this->forbiddenAsToken[$filename])) {
            return;
        }

        if (preg_match_all($regex, $this->file, $matches)) {
            array_walk($matches[2], function ($asName, $key) use ($matches, $path, $filename) {
                if (in_array($asName, $this->forbiddenAsToken[$filename])) {
                    Log::Error("You should not use ``$asName`` as the variable for loop!", false);
                    Log::Write("In $path");
                    Log::Write($matches[0][$key]);
                    Log::Error("Exited");
                }
            });
        }
    }

    /**
     * @param $object
     * @param $type
     */
    public function displayErrors($object, $type)
    {
        $function = ucfirst($type);
        Log::$function(
            'In Line ' . $object->getLine() .
            ', Col ' . $object->getColumn() .
            ", " .
            str_replace("\n", "", $object->getMessage()),
             false);
        Log::Write($object->getSource());
        Log::Line();
    }

    public function changeTheme()
    {
        global $zbp;
        global $app;
        Log::log("Changing Theme...");
        $this->origTheme = $zbp->option['ZC_BLOG_THEME'];
        $this->origCSS = $zbp->option['ZC_BLOG_CSS'];

        \SetTheme($app->id, array_keys($app->GetCssFiles())[0]);
        $zbp->BuildModule();
        $zbp->SaveCache();
        Log::log("Compiling Theme...");
        $zbp->CheckTemplate();
        Log::log("Theme changed!");
    }

    public function restoreTheme()
    {
        global $zbp;
        \SetTheme($this->origTheme, $this->origCSS);
        $zbp->SaveConfig('system');
    }

    public function checkW3C()
    {
        global $zbp;
        global $app;
        Log::Log("Initializing W3C...");
        $this->validateW3C($zbp->host);
        $this->validateW3C($this->articleUrl);
    }
    /**
     * Run Checker
     * @param string $filePath
     */
    public function runChecker($filePath)
    {
        $this->path = $filePath;
        $this->file = file_get_contents($this->path);
        $this->checkAs();
    }

    public function runBrowser()
    {
        global $zbp;
        $checkArray = [$zbp->host, $this->articleUrl];
        foreach ($checkArray as $item) {
            $ret = \AppChecker\Browser\Runner::runElectron($item);
            foreach ($ret['error'] as $error) {
                Log::error($error, false);
            }
            foreach ($ret['info'] as $error) {
                Log::info($error);
            }
        }
    }

    public function getOneArticle()
    {
        global $zbp;
        global $config;
        $sql = $zbp->db->sql->get()->select('%pre%post')->column('log_ID')->limit(1);
        if (isset($config->ArticleId)) {
            $sql->where(array("=", "log_ID", $config->ArticleId));
        }
        $query = $zbp->GetListType('Post', $sql->sql);
        if (count($query) == 0) {
            return;
        }
        $article = $query[0];
        $this->articleUrl = $article->Url;
    }

    /**
     * Run
     */
    public function run()
    {
        global $zbp;
        global $app;

        if ($app->type == 'plugin') {
            return;
        }

        Log::title('THEME STANDARD');
        // Log::Log('Scanning useless jQuery');
        $templateDir = $zbp->path . 'zb_users/theme/' . $app->id . '/template/';
        foreach (Utils::scanDirectory($templateDir) as $index => $value) {
            $this->runChecker($value);
        }

        $this->changeTheme();
        $this->getOneArticle();
        $this->runBrowser();
        $this->checkW3C();
        $this->restoreTheme();
    }
}
