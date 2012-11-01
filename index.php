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


	define('TAG_REGEX', '/(^|\s|\()(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms');


	app\any('.*', function($req) {
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});


	app\get('/', function() {

		$user = mysql\row('SELECT name, email, bio FROM users where id = 1');
		$channels = mysql\rows('select name, count(*) as count from channels where user_id = 1 and private = 0 group by name order by count desc');
		$md_posts = mysql\rows('select * from posts where user_id = 1 and private = 0 order by created_at desc limit 10');

		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = preg_replace(TAG_REGEX, '$1<span class="hash">#</span><a href="channels/$3" rel="tag">$3</a>', $content);
			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('user', 'channels', 'posts'), 'layout.html');
	});

# TODO: Remove this duplication of routes:
	app\path_macro(array('/post[/[{id}]]', '/signout', '/persona-verifier'), function() {

		require __DIR__.'/post.php';
	});


# TODO: look at AtomPub?

	app\get('/posts/[{id}]', function() {

		return "Coming Soon...";
	});

	app\get('/channels/[{id}]', function() {

		return "Coming Soon...";
	});

?>