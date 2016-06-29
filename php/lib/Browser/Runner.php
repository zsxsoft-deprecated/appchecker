<?php
namespace AppChecker\Browser;

class Runner {
    public static function RunElectron() {
        global $zbp;
        $ret = [];
        $execJavaScript = escapeshellarg(str_replace("\n", "", "
{
function detect(message, detectFunction) {
	if (detectFunction()) {
		ElectronRet.success.push('Detected: ' + message);
	} else {
		ElectronRet.error.push('Not Detected: ' + message);
	}
};
detect('typeof RevertComment === undefined', () => {return typeof RevertComment === 'undefined'});
detect('typeof GetComments === undefined', () => {return typeof GetComments === 'undefined'});
detect('typeof VerifyMessage === undefined', () => {return typeof VerifyMessage === 'undefined'});

{
	try {
		ElectronRet.info.push('jQuery Version: ' + jQuery.fn.jquery); 
	} catch (e) {
		ElectronRet.error.push(e.toString());
	}
	
}

require('electron').ipcRenderer.send('message', ElectronRet);
require('electron').remote.getCurrentWindow().destroy();
}
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
