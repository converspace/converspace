<?php

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/template/template.php';

	use phpish\app;
	use phpish\template;


	app\get('/', function() {

		return template\render('index.html');
	});

?>