<?php


	define('APPLICATION_ENV', preg_match('/^127\.0\.0\.1.*/', $_SERVER['HTTP_HOST']) ? 'development' : 'production');
	require __DIR__.'/conf/'.APPLICATION_ENV.'.conf.php';

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/mysql/mysql.php';
	require __DIR__.'/vendor/phpish/template/template.php';


	use phpish\app;
	use phpish\mysql;
	use phpish\template;


	app\any('.*', function($req) {
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});


	app\get('/', function() {

		return template\compose('index.html', 'layout.html');
	});

# TODO: Remove this duplication of routes:
	app\path_macro(array('/post[/[{id}]]', '/signout', '/persona-verifier'), function() {

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