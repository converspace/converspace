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
	require __DIR__.'/url_to_absolute.php';


	use phpish\app;
	use phpish\mysql;
	use phpish\template;
	use phpish\http;
	use mf2\Parser;


	require __DIR__.'/data.php';
	require __DIR__.'/helpers.php';
	require __DIR__.'/webmention.php';
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


	app\get('/mentions', function($req, $authorized) {
		$mentions = get_all_webmentions();
		return template\compose('mentions.html', compact('mentions', 'authorized'), 'layout.html');
	});


	app\any(array('/s/.*', '/post', '/send-webmention', '/send-webmention/{post_id:digits}'), function($req) {

		if (!isset($_SESSION['user']))
		{
			session_alert('error', 'You are not authorized to post.');
			return app\response_302(SITE_BASE_URL);
		}
		else return app\next($req);

	});


	app\query('/s/publish', function($req) {
		if (isset($req['query']['url']))
		{
			$url = $req['query']['url'];
			$response_body = http\request("GET $url", array(), array(), $response_headers);
			$mf2 = webmention\get_mf2($response_body, $url);
			$h_entry = webmention\get_h_entry($mf2);
			print_r($h_entry);
			print_r(webmention\get_h_card($h_entry['properties'], $mf2, $url));
			exit;
		}
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
		foreach ($req['form']['targets'] as $target) $responses[] = webmention\send($req['form']['source'], $target);
		print_r($responses);
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
				if (is_valid_post($matches['post_id']))
				{
					try
					{
						$response_body = http\request("GET $source", array(), array(), $response_headers);
						if (webmention\source_links_to_target($response_body, $target))
						{
							$hentry = webmention\get_mf2_data($response_body, $source, $target);
							$published = isset($hentry['published']) ? date('Y-m-d H:i:s', strtotime($hentry['published'])) : '';
							add_webmention($matches['post_id'], $source, md5($source), $target, md5($target), date('Y-m-d H:i:s'), @$hentry['type'], @$hentry['content'], @$hentry['author']['name'], @$hentry['author']['url'], @$hentry['author']['photo'], $published);
							return app\response(json_pretty_print(json_encode($hentry)), 200, array('content-type'=>'application/json; charset=utf-8'));
						}
						else app\response('Source URL does not contain a link to the target URL.', 400);
					}
					catch (http\ResponseException $e)
					{
						return app\response('Source URL not found.', 400);
					}
				}
				else return app\response('Specified target URL not found.', 400);
			}
		}

		return app\response('Bad Request', 400);
	});

		function is_valid_post($post_id)
		{
			list($posts, $pager) = get_post($post_id, true);
			return !empty($posts);
		}


	// test with curl -I <URL>
	app\any('/{post_id:digits}[\.{content_type}]', function($req, $authorized=false) {
		$response =  app\next($req, $authorized);
		if (is_array($response) and isset($response['headers']))
		{
			$response['headers']['Link'] = '<'.SITE_BASE_URL.'webmention>; rel="http://webmention.org/"';
			return $response;
		}
		else return app\response($response, 200, array
		(
			'Link'=>'<'.SITE_BASE_URL.'webmention>; rel="http://webmention.org/"'
		));
	});

	app\get('/{post_id:digits}[\.{content_type}]', function($req, $authorized=false) {

		$individual_post = true;
		list($posts, $pager) = get_post($req['matches']['post_id'], $authorized);
		$mention_count = array();
		$mention_count[$req['matches']['post_id']] = get_webmention_type_counts($req['matches']['post_id']);

		$content = template\compose('index.html', compact('authorized', 'posts', 'pager', 'individual_post', 'mention_count'), 'layout.html');

		if (isset($req['matches']['content_type']))
		{
			$mf2parser = new Parser($content);
			$mf2 = $mf2parser->parse();

			if ('mf2' == $req['matches']['content_type'])
			{
				return app\response(json_pretty_print(json_encode($mf2)), 200, array('content-type'=>'application/json; charset=utf-8'));
			}
			elseif ('as' == $req['matches']['content_type'])
			{
				return app\response(template\render('index.as', compact('mf2')), 200, array('content-type'=>'application/json; charset=utf-8'));
			}
		}

		return $content;
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