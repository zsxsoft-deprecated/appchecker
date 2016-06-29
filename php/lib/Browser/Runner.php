<?php
namespace AppChecker\Browser;

class Runner {
    public static function RunElectron() {
        global $zbp;
        $ret = [];
        $execJavaScript = escapeshellarg(str_replace("\n", "", "
require('electron').ipcRenderer.send('message', {message: 'PHP'});
require('electron').remote.getCurrentWindow().destroy();
"));

        $dirName = realpath(dirname(__FILE__) . '/../../../browser/');
        $execGlobal = realpath($dirName . '/browser.exe');
        if ($execGlobal === false) {
            $execGlobal = "electron " . $dirName;
        }
        $execGlobal .= ' -u "' . $zbp->host. '" ';
        $execGlobal .= ' -r ' . $execJavaScript . ' ';

        exec($execGlobal, $ret);
        var_dump($ret);
    }
}
