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

		$row = mysql\row('SELECT email FROM users where id = 1');
		if (!empty($row)) $email = $row['email'];
		else $email = '';

		$channels = mysql\rows('select channel, count(*) as count from channels group by channel order by count desc');

		$posts = mysql\rows('select * from posts order by created_at desc limit 10');

		/*
		TODO: Might want to directly use <a> so that I can add a rel attribute.
		if (substr($post, 0, 2) == '# ') list($title, $post) = preg_split('/\n/', $post, 2);
		$linkified_channels = preg_replace('/(?:^|\s)(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms', ' [$1](channels/$2)', $post);
		$post = "$title\n$post";
		*/

		return template\compose('index.html', compact('email', 'channels', 'posts'), 'layout.html');
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