<?php
namespace AppChecker\Scanner;

use AppChecker\Log as Log;
use AppChecker\Utils;

class Template {
    private $file = "";
    private $path = "";
    private $forbiddenAsToken = [];

    private $origTheme = "";
    private $origCSS = "";

    private $articleUrl = "";

    public function __construct() {
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
     */
    public function ValidateW3C($url) {
        Log::Log('Testing ' . $url);
        ob_flush();
        $validator = new \W3C\HtmlValidator();
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
            Log::Warning('Validation failed: ' . $result->getErrorCount() . " error(s) and " . $result->getWarningCount() . ' warning(s).');
        }
    }

    /**
     * Check Error `As`
     */
    public function CheckAs() {
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
    public function DisplayErrors($object, $type) {
        $function = ucfirst($type);
        Log::$function('In Line ' . $object->getLine() . ', Col ' . $object->getColumn() . ", " . str_replace("\n", "", $object->getMessage()), false);
        Log::Write($object->getSource());
        Log::Line();
    }

    public function ChangeTheme() {
        global $zbp;
        global $app;
        Log::Log("Changing Theme...");
        $this->origTheme = $zbp->option['ZC_BLOG_THEME'];
        $this->origCSS = $zbp->option['ZC_BLOG_CSS'];
        $zbp->Config('system')->ZC_BLOG_THEME = $app->id;
        $zbp->Config('system')->ZC_BLOG_CSS = array_keys($app->GetCssFiles())[0];
        $zbp->SaveConfig('system');

        $template = $zbp->PrepareTemplate($app->id);
        $template->LoadTemplates();
        $zbp->BuildTemplate($template);
        
    }

    public function RestoreTheme() {
        global $zbp;
        $zbp->Config('system')->ZC_BLOG_THEME = $this->origTheme;
        $zbp->Config('system')->ZC_BLOG_CSS = $this->origCSS;
        $zbp->SaveConfig('system');
    }

    public function CheckW3C() {
        global $zbp;
        global $app;
        Log::Log("Initializing W3C...");
        $this->ValidateW3C($zbp->host);
        $this->ValidateW3C($this->articleUrl);
    }
    /**
     * Run Checker
     * @param string $path
     */
    public function RunChecker($filePath) {
        $this->path = $filePath;
        $this->file = file_get_contents($this->path);
        $this->CheckAs();
    }


    public function RunBrowser() {
        global $zbp;
        $checkArray = [$zbp->host, $this->articleUrl];
        foreach ($checkArray as $item) {
            $ret = \AppChecker\Browser\Runner::RunElectron($item);
            foreach ($ret['error'] as $error) {
                Log::Error($error, false);
            }
            foreach ($ret['info'] as $error) {
                Log::Info($error);
            }
        }
        
    }

    public function GetOneArticle() {
        global $zbp;
        $sql = $zbp->db->sql->get()->select('%pre%post')->column('log_ID')->limit(1)->sql;
        $query = $zbp->GetListType('Post', $sql);
        if (count($query) == 0) {
            return;
        }
        $article = $query[0];
        $this->articleUrl = $article->Url;
    }

    /**
     * Run
     */
    public function Run() {
        global $zbp;
        global $app;

        if ($app->type == 'plugin') {
            return;
        }

        Log::Title('THEME STANDARD');
        // Log::Log('Scanning useless jQuery');
        $templateDir = $zbp->path . 'zb_users/theme/' . $app->id . '/template/';
        foreach (Utils::ScanDirectory($templateDir) as $index => $value) {
            $this->RunChecker($value);
        }

        $this->ChangeTheme();
        $this->GetOneArticle();
        $this->RunBrowser();
        $this->CheckW3C();
        $this->RestoreTheme();
    }

}
