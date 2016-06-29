'use strict';
{
	const commander = require('commander');
	const app = require('koa')();
	const router = require('koa-router')();
	const packageJson = require('./package.json');
	commander
		.version(packageJson.version)
		.option('-b, --bind', 'Listening port', 30000)
		.parse(process.argv);

	router.post("/", function *(next) {
		
	});


	app.use(router.routers()).use(router.allowedMethods());

	console.log(`Server started at: 127.0.0.1:${commander.bind}`);
	app.listen(commander.bind);
}