<?php


	define('APPLICATION_ENV', preg_match('/^127\.0\.0\.1.*/', $_SERVER['HTTP_HOST']) ? 'development' : 'production');
	require __DIR__.'/conf/'.APPLICATION_ENV.'.conf.php';

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/mysql/mysql.php';
	require __DIR__.'/vendor/phpish/template/template.php';
	require __DIR__.'/vendor/michelf/php-markdown-extra/markdown.php';


	use phpish\app;
	use phpish\mysql;
	use phpish\template;


	app\any('.*', function($req) {
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});


	app\get('/', function() {

		$row = mysql\row('SELECT name, email FROM users where id = 1');
		if (!empty($row))
		{
			$email = $row['email'];
			$name = $row['name'];
		}
		else $email = $name = '';

		$channels = mysql\rows('select name, count(*) as count from channels where user_id = 1 and private = 0 group by name order by count desc');
		$md_posts = mysql\rows('select * from posts where user_id = 1 and private = 0 order by created_at desc limit 10');

		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = preg_replace('/(?:^|\s)(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms', ' <a href="#channels/$2" rel="tag">$1</a>', $content);
			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('name', 'email', 'channels', 'posts'), 'layout.html');
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