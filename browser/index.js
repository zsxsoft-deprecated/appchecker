'use strict';
{
    const commander = require('commander');
    const { app, BrowserWindow, ipcMain } = require('electron');
    const packageJson = require('./package.json');
    let stdout = {};

    commander
        .version(packageJson.version)
        .option('-u, --url [url]', 'Navigate Url', 'http://localhost/')
        .option('-r, --code [code]', 'Run Code', "require('electron').ipcRenderer.send('message', {message: 'OK'});require('electron').remote.getCurrentWindow().destroy();")
        .parse(process.argv);

    ipcMain.on('message', (event, arg) => {
    	stdout = Object.assign(stdout, arg);
    });
    app.on('ready', () => {
        let win = new BrowserWindow({ show: false });
        win.on('closed', () => {
            win = null;
            process.stdout.write(JSON.stringify(stdout));
            process.exit();
        });
        win.webContents.once('did-finish-load', () => {
            win.webContents.executeJavaScript(commander.code);
        });
        win.loadURL(commander.url);
    });


}
