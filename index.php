<?php

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/template/template.php';

	use phpish\app;
	use phpish\template;


	app\get('/', function() {

		return template\render('index.html');
	});


	app\get('/post', function() {

		# temp interface for posting
	});


	app\post('/post', function() {

		# temp interface for posting
	});


# TODO: look at AtomPub?

	app\get('/posts/[{id}]', function() {

		# get post from db
		# convert hashtags into links
		# apply markdown
	});

	app\get('/channels/[{id}]', function() {

		# /channels/ is ordered by count of posts in the channel. Used by the channel sidebar.
	});

?>