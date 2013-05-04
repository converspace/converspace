<?php


	define('APPLICATION_ENV', preg_match('/^127\.0\.0\.1.*/', $_SERVER['HTTP_HOST']) ? 'development' : 'production');
	require __DIR__.'/conf/'.APPLICATION_ENV.'.conf.php';

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/mysql/mysql.php';
	require __DIR__.'/vendor/phpish/template/template.php';
	require __DIR__.'/vendor/phpish/http/http.php';
	require __DIR__.'/vendor/michelf/php-markdown-extra/markdown.php';


	use phpish\app;
	use phpish\mysql;
	use phpish\template;
	use phpish\http;


	require __DIR__.'/data.php';
	require __DIR__.'/helpers.php';
	require __DIR__.'/models/posts.php';


	app\any('.*', function($req) {
		session_start();
		mysql\connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME);
		return app\next($req);
	});



	app\post('/post', function($req) {

		$_SESSION['alert'] = array();

		if (!isset($_SESSION['user']))
		{
			$_SESSION['alert']['msg'] = 'You are not authorized to post.';
			$_SESSION['alert']['type'] = 'error';

			return app\response_302(SITE_BASE_URL);
		}
		else return app\next($req);

	});


	app\post('/post', function($req) {

		$post_content = $req['form']['post']['content'];

		if (!empty($post_content))
		{
			$is_private = isset($req['form']['private']) ? 1 : 0;

			$now = date('Y-m-d H:i:s');

			$post_body = '';
			if (substr($post_content, 0, 2) == '# ') list($post_title, $post_body) = preg_split('/\n/', $post_content, 2);
			else $post_body = $post_content;
			preg_match_all(TAG_REGEX, $post_body, $matches);
			$post_channels = $matches[3];


			if (isset($req['form']['post']['id']))
			{
				$post_id = $req['form']['post']['id'];
				db_update_post($post_id, $post_content, $now, $is_private);
				if (mysql\affected_rows() === 1)
				{
					$channels_to_delete = array();
					$existing_channels_rows = db_get_post_channels($post_id);
					foreach ($existing_channels_rows as $existing_channel_row)
					{
						if (false === ($key = array_search($existing_channel_row['name'], $post_channels)))
						{
							$channels_to_delete[] = $existing_channel_row['name'];
						}
						else unset($post_channels[$key]);
					}

					if (!empty($channels_to_delete)) delete_post_channels($post_id, $channels_to_delete);

					foreach($post_channels as $channel_name)
					{
						db_add_post_channel($post_id, $channel_name, $now, $is_private);
					}
				}

			}
			else
			{
				db_add_post($post_content, $now, $is_private);
				if (mysql\affected_rows() === 1)
				{
					$post_id = mysql\insert_id();

					foreach($post_channels as $channel_name)
					{
						db_add_post_channel($post_id, $channel_name, $now, $is_private);
					}

					$_SESSION['alert']['msg'] = 'Post Saved!';
					$_SESSION['alert']['type'] = 'success';
				}
				else
				{
					error_log('Error while saving post: '.mysql\error());
					$_SESSION['alert']['msg'] = 'Sorry! Error while saving post! ';
					$_SESSION['alert']['type'] = 'error';
				}
			}
		}

		return app\response_302(SITE_BASE_URL);

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


	app\get('/{post_id:digits}', function($req) {

		$individual_post = true;
		list($posts, $pager) = get_post($req['matches']['post_id']);
		return template\compose('index.html', compact('posts', 'pager', 'individual_post'), 'layout.html');
	});


	app\get('/[{channel}/]', function($req) {

		$channel_name = isset($req['matches']['channel']) ? $req['matches']['channel'] : '';
		list($posts, $pager) = get_posts($req, $channel_name);
		return template\compose('index.html', compact('posts', 'pager', 'channel_name'), 'layout.html');
	});

?>