<?php
namespace AppChecker\Browser;

class Runner {
    public static function RunElectron($url) {
        global $zbp;
        $ret = [];
        $execJavaScript = escapeshellarg(str_replace("\n", "", "
{
let { ipcRenderer, remote } = require('electron');
let current = remote.getCurrentWindow();

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

ipcRenderer.send('message', ElectronRet);
remote.getCurrentWindow().destroy();
}
"));

        $dirName = realpath(dirname(__FILE__) . '/../../../browser/');
        $execGlobal = realpath($dirName . '/browser.exe') . " . ";
        if ($execGlobal === false) {
            $execGlobal = "electron " . $dirName;
        }
        $execGlobal .= ' -u "' . $url . '" ';
        $execGlobal .= ' -r ' . $execJavaScript . ' ';
        $execGlobal .= ' -s ' . escapeshellarg($zbp->path . '/screenshot-' . time() . '.jpg');

        exec($execGlobal, $ret);

        $newArray = array_filter($ret);
        $ret = null;
        foreach ($newArray as $item) {
            try {
                $ret = json_decode($item, true);
                break;
            } catch (Exception $e) {
                continue;
            }
        }

        return $ret;
    }

}
