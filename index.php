<?php

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/template/template.php';


	use phpish\app;
	use phpish\template;


	app\get('/', function() {

		return template\compose('index.html', 'layout.html');
	});

	app\path_macro(array('/post', '/signout', '/persona-verifier'), function() {

		require __DIR__.'/post.php';
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