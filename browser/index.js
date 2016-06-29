'use strict';
{
    
    global.commander = require('commander');
    const { app, BrowserWindow, ipcMain } = require('electron');
    const deepAssign = require('deep-assign');
    const packageJson = require('./package.json');
    const screenshot = require('electron-screenshot-app');

    const commander = global.commander;

    let exitCount = 0;
    let stdout = {
    	error: [], 
    	info: [], 
    	success: []
    };

    function addExitCount() {
    	if (++exitCount === 2) {
    		process.exit(0);
    	}
    }

    commander
        .version(packageJson.version)
        .option('-u, --url [url]', 'Navigate Url', 'http://localhost/')
        .option('-s, --screenshotPath [path]', 'Screenshot path')
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
            addExitCount();
        });
        win.webContents.once('did-finish-load', () => {
            win.webContents.executeJavaScript(commander.code);
        })
        win.webContents.once('dom-ready', () => {
        	win.webContents.executeJavaScript(`(${domReady.toString()})()`);
        });
        win.loadURL(commander.url);

        screenshot({
        	url: commander.url, 
        	height: 1080, 
        	width: 1920,
        	page: true
        }, (err, image) => {
        	require("fs").writeFile(`./${new Date().getTime()}.png`, image.data);
        	addExitCount();
        });
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
