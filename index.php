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


	require __DIR__.'/data.php';
	require __DIR__.'/helpers.php';


	app\any('.*', function($req) {
		session_start();
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});


	app\get('/', function($req) {

		if (isset($req['query']['before'])) $before = intval($req['query']['before']);
		elseif (isset($req['query']['after'])) $after = intval($req['query']['after']);
		$pager = array();

		$channels = get_channels();

		if (isset($before))
		{
			$md_posts = get_posts_before($before);
			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['after'] = $md_posts[0]['id'];
		}
		elseif (isset($after))
		{
			$md_posts = get_posts_after($after);
			if (count($md_posts) === 11)
			{
				$pager['after'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['before'] = $md_posts[0]['id'];
			$md_posts = array_reverse($md_posts);
		}
		else
		{
			$md_posts = get_posts();
			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
		}


		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = tag_syntax_filter($content);
			$content = twitter_user_syntax_filter($content);

			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('title'=>$title, 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('channels', 'posts', 'pager'), 'layout.html');
	});

# TODO: Remove this duplication of routes:
	app\path_macro(array('/post[/[{id}]]', '/signout', '/persona-verifier'), function() {

		require __DIR__.'/post.php';
	});


# TODO: look at AtomPub?

	app\get('/posts/[{post_id}]', function($req) {

		$post_edit = true;
		$channels = get_channels();
		$md_posts = get_post($req['matches']['post_id']);
		$post_neighbours['older'] = get_older_post_id($req['matches']['post_id']);
		$post_neighbours['newer'] = get_newer_post_id($req['matches']['post_id']);


		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = tag_syntax_filter($content);
			$content = twitter_user_syntax_filter($content);

			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('title'=>$title, 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('channels', 'posts', 'post_edit', 'post_neighbours'), 'layout.html');
	});

	app\get('/channels/[{channel}]', function($req) {

		$channel_name = $req['matches']['channel'];
		if (isset($req['query']['before'])) $before = intval($req['query']['before']);
		elseif (isset($req['query']['after'])) $after = intval($req['query']['after']);
		$pager = array();

		$channels = get_channels();

		if (isset($before))
		{
			$md_posts = get_channel_posts_before($channel_name, $before);
			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['after'] = $md_posts[0]['id'];
		}
		elseif (isset($after))
		{
			$md_posts = get_channel_posts_after($channel_name, $after);
			if (count($md_posts) === 11)
			{
				$pager['after'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['before'] = $md_posts[0]['id'];
			$md_posts = array_reverse($md_posts);
		}
		else
		{
			$md_posts = get_channel_posts($channel_name);

			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
		}


		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = tag_syntax_filter($content);
			$content = twitter_user_syntax_filter($content);

			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('title'=>$title, 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('channel_name', 'channels', 'posts', 'pager'), 'layout.html');
	});

?>