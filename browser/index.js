'use strict';
{
    const commander = require('commander');
    const { app, BrowserWindow, ipcMain } = require('electron');
    const deepAssign = require('deep-assign');
    const packageJson = require('./package.json');
    let stdout = {
    	error: [], 
    	info: [], 
    	success: []
    };

    commander
        .version(packageJson.version)
        .option('-u, --url [url]', 'Navigate Url', 'http://localhost/')
        .option('-r, --code [code]', 'Run Code', "require('electron').ipcRenderer.send('message', {message: 'OK'});require('electron').remote.getCurrentWindow().destroy();")
        .parse(process.argv);

    ipcMain.on('message', (event, arg) => {
    	stdout = deepAssign(stdout, arg);
    });
    app.on('ready', () => {
        let win = new BrowserWindow({ show: false });
        win.on('closed', () => {
            win = null;
            process.stdout.write(JSON.stringify(stdout));
            process.exit(0);
        });
        win.webContents.once('did-finish-load', () => {
            win.webContents.executeJavaScript(commander.code);
        })
        win.webContents.once('dom-ready', () => {
        	win.webContents.executeJavaScript(`(${domReady.toString()})()`);
        });
        win.loadURL(commander.url);
    });


    let domReady = function() {
    	window.ElectronRet = {error: [], success: [], info: []}; 
    	window.addEventListener("error", (e) => {
    		let message = e.message;
    		if (!message) {
    			message = e.target.src + " load error";
    		}
    		ElectronRet.error.push(message);
    	}, true);
    }

}
