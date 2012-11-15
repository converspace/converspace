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
	define('TWITTER_USER_REGEX', '/(^|\s|\()(@([a-zA-Z0-9_]+))/ms');


	function gravatar($email, $s=80, $d='mm', $r='g', $img=false, $atts=array())
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img )
		{
			$url = "<img src=\"$url\"";
			foreach ($atts as $key=>$val) $url .= " $key=\"$val\"";
			$url .= ' />';
		}

		return $url;
	}


	app\any('.*', function($req) {
		session_start();
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});


	app\get('/', function($req) {

		if (isset($req['query']['before'])) $before = intval($req['query']['before']);
		elseif (isset($req['query']['after'])) $after = intval($req['query']['after']);
		$pager = array();

		$channels = mysql\rows('SELECT name, count(*) as count FROM channels WHERE private = 0 GROUP BY name ORDER BY count DESC');

		if (isset($before))
		{
			$md_posts = mysql\rows('SELECT * FROM posts WHERE private = 0 AND id < %d ORDER BY id DESC LIMIT 11', array($before));
			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['after'] = $md_posts[0]['id'];
		}
		elseif (isset($after))
		{
			$md_posts = mysql\rows('SELECT * FROM posts WHERE private = 0 AND id > %d ORDER BY id ASC LIMIT 11', array($after));
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
			$md_posts = mysql\rows('SELECT * FROM posts WHERE private = 0 ORDER BY id DESC LIMIT 11');
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

			$content = preg_replace(TAG_REGEX, '$1<span class="deem">#</span><a href="'.SITE_BASE_URL.'channels/$3" rel="tag">$3</a>', $content);
			$content = preg_replace(TWITTER_USER_REGEX, '$1<span class="deem">@</span><a href="https://twitter.com/$3">$3</a>', $content);

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
		$channels = mysql\rows('select name, count(*) as count from channels where private = 0 group by name order by count desc');
		$md_posts = mysql\rows("select * from posts where id = %d", array($req['matches']['post_id']));
		$post_neighbours['older'] = mysql\row("select max(id) as id from posts where id < %d", array($req['matches']['post_id']));
		$post_neighbours['newer'] = mysql\row("select min(id) as id from posts where id > %d", array($req['matches']['post_id']));


		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = preg_replace(TAG_REGEX, '$1<span class="deem">#</span><a href="'.SITE_BASE_URL.'channels/$3" rel="tag">$3</a>', $content);
			$content = preg_replace(TWITTER_USER_REGEX, '$1<span class="deem">@</span><a href="https://twitter.com/$3">$3</a>', $content);

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

		$channels = mysql\rows('SELECT name, count(*) as count FROM channels WHERE private = 0 GROUP BY name ORDER BY count DESC');

		if (isset($before))
		{
			$md_posts = mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id < %d AND p.private = 0 ORDER BY id DESC LIMIT 11", array($channel_name, $before));
			if (count($md_posts) === 11)
			{
				$pager['before'] = $md_posts[9]['id'];
				array_pop($md_posts);
			}
			$pager['after'] = $md_posts[0]['id'];
		}
		elseif (isset($after))
		{
			$md_posts = mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id > %d AND p.private = 0 ORDER BY id ASC LIMIT 11", array($channel_name, $after));
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
			$md_posts = mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.private = 0 ORDER BY p.id DESC LIMIT 11", array($channel_name));

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

			$content = preg_replace(TAG_REGEX, '$1<span class="deem">#</span><a href="'.SITE_BASE_URL.'channels/$3" rel="tag">$3</a>', $content);
			$content = preg_replace(TWITTER_USER_REGEX, '$1<span class="deem">@</span><a href="https://twitter.com/$3">$3</a>', $content);

			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('title'=>$title, 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return template\compose('index.html', compact('channel_name', 'channels', 'posts', 'pager'), 'layout.html');
	});


	function dopplr_color($str)
	{
		return substr(md5($str), 0, 6);
	}

?>