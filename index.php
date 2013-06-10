<?php


	define('APPLICATION_ENV', preg_match('/^127\.0\.0\.1.*/', $_SERVER['HTTP_HOST']) ? 'development' : 'production');
	require __DIR__.'/conf/'.APPLICATION_ENV.'.conf.php';

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/mysql/mysql.php';
	require __DIR__.'/vendor/phpish/template/template.php';
	require __DIR__.'/vendor/phpish/http/http.php';
	require __DIR__.'/vendor/michelf/php-markdown/Michelf/Markdown.php';
	require __DIR__.'/vendor/michelf/php-markdown/Michelf/MarkdownExtra.php';
	require __DIR__.'/vendor/autoload.php';


	use phpish\app;
	use phpish\mysql;
	use phpish\template;
	use phpish\http;
	use mf2\Parser;


	require __DIR__.'/data.php';
	require __DIR__.'/helpers.php';
	require __DIR__.'/models/posts.php';



	// Cool URIs don't change
		app\get('/posts/{post_id:digits}', function($req) {
			return app\response_302(SITE_BASE_URL.$req['matches']['post_id']);
		});

		app\get('/channels/{channel}', function($req) {
			return app\response_302(SITE_BASE_URL.$req['matches']['channel'].'/');
		});


	app\any('.*', function($req) {
		session_start();
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		$authorized = isset($_SESSION['user']);
		return app\next($req, $authorized);
	});


	app\post('/signout', function() {

		unset($_SESSION['persona'], $_SESSION['user']);
		session_destroy();
	});


	app\post('/persona-verifier', function($req) {

		if (isset($req['form']['assertion']))
		{
			$response = http\request(
				"POST https://verifier.login.persona.org/verify",
				'',
				//TODO: Remove hardcoded audience 127.0.0.1
				array('assertion'=>$req['form']['assertion'], 'audience'=>PERSONA_AUDIENCE)
			);

			if ('okay' == $response['status'])
			{
				$_SESSION['persona'] = $response;
				if (USER_EMAIL == $response['email']) $_SESSION['user'] = $response;
				else error_log('Somebody logged in.');
			}
		}
	});


	app\any(array('/post', '/send-webmention', '/send-webmention/{post_id:digits}'), function($req) {

		if (!isset($_SESSION['user']))
		{
			session_alert('error', 'You are not authorized to post.');
			return app\response_302(SITE_BASE_URL);
		}
		else return app\next($req);

	});


	app\post('/post', function($req) {

		$post_content = $req['form']['post']['content'];

		if (!empty($post_content))
		{
			$now = date('Y-m-d H:i:s');
			$post = extract_title_and_body_from_post($post_content);

			$machinetags = extract_machinetags($post['body']);
			$post['body'] = strip_machinetags($post['body'], $machinetags[0]);

			$post_channels = extract_tags_from_post($post['body']);
			$is_private = in_array('private', $post_channels) ? 1 : 0;

			if (isset($req['form']['post']['id']))
			{
				$post_id = $req['form']['post']['id'];
				update_post($post_id, $post_content, $now, $is_private, $post_channels);
			}
			else
			{
				$post_id = add_post($post_content, $now, $is_private, $post_channels);
			}
		}

		return ($post_id) ? app\response_302(SITE_BASE_URL.$post_id) : app\response_302(SITE_BASE_URL);

	});


	app\post('/send-webmention', function($req) {
		foreach ($req['form']['targets'] as $target) $response[] = send_webmention($req['form']['source'], $target);
	});


	app\get('/send-webmention/{post_id:digits}', function($req) {
		$post_id = $req['matches']['post_id'];
		list($posts, $pager) = get_post($post_id, true);
		$post = $posts[0];
		$dom = new DOMDocument;
		@$dom->loadHTML($post['content']);
		$links = $dom->getElementsByTagName('a');
		echo '<form method="post" action="'.SITE_BASE_URL.'send-webmention">';
		echo '<input type="hidden" name="source" value="'.SITE_BASE_URL.$post_id.'">';
		foreach ($links as $link){
			$link_href = $link->getAttribute('href');
			echo '<input type="checkbox" name="targets[]" value="'.$link_href.'">'.$link_href.'<br>';
		}
		echo '<input type="submit" name="action" value="Send WebMentions">';
		echo '</form>';
	});


	// test with curl -i -d "source=foo.com&target=bar.com" <webmention-endpoint>
	app\post('/webmention', function($req) {

		if (isset($req['form']['target']) and isset($req['form']['source']) )
		{
			$source = $req['form']['source'];
			$target = $req['form']['target'];

			if (preg_match('#^'.SITE_BASE_URL.'(?P<post_id>[0-9]+)$#', $target, $matches))
			{

				list($posts, $pager) = get_post($matches['post_id'], true);
				if (!empty($posts))
				{
					$response_body = http\request("GET $source", array(), array(), $response_headers);
					$mf2parser = new Parser($response_body);
					$mf2 = $mf2parser->parse();
					$type = 'mention';
					foreach ($mf2['items'] as $item)
					{
						if (in_array('h-entry', $item['type']))
						{
							if (isset($item['properties']['repost'])) $type = 'repost';
							if (isset($item['properties']['like'])) $type = 'like';
							if (isset($item['properties']['in-reply-to'])) $type = 'in-reply-to';
						}
					}

					add_webmention($matches['post_id'], $source, md5($source), $target, md5($target), date('Y-m-d H:i:s'), $type, $response_body);
					return app\response($mf2, 202);
				}
			}
		}

		return app\response('Bad Request', 400);
	});


	// test with curl -I <URL>
	app\any('/{post_id:digits}', function($req, $authorized=false) {
		$response =  app\next($req, $authorized);
		return app\response($response, 200, array
		(
			"Link"=>'<'.SITE_BASE_URL.'webmention>; rel="http://webmention.org/"'
		));
	});

	app\get('/{post_id:digits}', function($req, $authorized=false) {

		$individual_post = true;
		list($posts, $pager) = get_post($req['matches']['post_id'], $authorized);
		$mention_count = array();
		$mention_count[$req['matches']['post_id']] = get_webmention_type_counts($req['matches']['post_id']);
		return template\compose('index.html', compact('authorized', 'posts', 'pager', 'individual_post', 'mention_count'), 'layout.html');
	});


	app\get('/{post_id:digits}/likes', function($req, $authorized=false) {

		return app\response(get_webmentions($req['matches']['post_id'], 'like'));
	});

	app\get('/{post_id:digits}/reposts', function($req, $authorized=false) {

		return app\response(get_webmentions($req['matches']['post_id'], 'repost'));
	});

	app\get('/{post_id:digits}/mentions', function($req, $authorized=false) {

		return app\response(get_webmentions($req['matches']['post_id'], 'mention'));
	});

	app\get('/{post_id:digits}/comments', function($req, $authorized=false) {

		return app\response(get_webmentions($req['matches']['post_id'], 'in-reply-to'));
	});


	app\get('/[{channel}/]', function($req, $authorized=false) {

		$channel_name = isset($req['matches']['channel']) ? $req['matches']['channel'] : '';
		list($posts, $pager) = get_posts($req, $channel_name, $authorized);
		$mention_count = array();
		foreach ($posts as $post)
		{
			$mention_count[$post['id']] = get_webmention_type_counts($post['id']);
		}
		return template\compose('index.html', compact('authorized', 'posts', 'pager', 'channel_name', 'mention_count'), 'layout.html');
	});

?>