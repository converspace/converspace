<?php

	require __DIR__.'/vendor/phpish/http/http.php';

	use phpish\app;
	use phpish\mysql;
	use phpish\template;
	use phpish\http;


	app\get('/post[/[{id}]]', function($req) {

		# TODO: /post/123 breaks relative paths for css, js, etc. Need to convert them to absolute paths
		# TODO: Edit interface for $req['matches']['id']

		$template_vars = array();

		if (!isset($_SESSION['persona']))
		{
			$template_vars['alert'] = 'Sign in to post.	';
			$template_vars['alert_type'] = 'info';
		}
		elseif (!isset($_SESSION['user']))
		{
			$template_vars['alert'] = 'You are not authorized to post.';
			$template_vars['alert_type'] = 'error';
		}

		return template\compose('post.html', $template_vars, 'layout.html');
	});


	app\post('/post', function($req) {

		$_SESSION['alert'] = array();

		if (!isset($_SESSION['user']))
		{
			$_SESSION['alert']['msg'] = 'You are not authorized to post.';
			$_SESSION['alert']['type'] = 'error';
		}
		else
		{
			$is_private = isset($req['form']['private']) ? 1 : 0;
			$post = $req['form']['post'];
			$now = date('Y-m-d H:i:s');
			mysql\query("INSERT INTO posts (user_id, content, created_at, updated_at, private) VALUES (1, '%s', '%s', '%s', %d)", array($post, $now, $now, $is_private));
			if (mysql\affected_rows() === 1)
			{
				$post_id = mysql\insert_id();
				if (substr($post, 0, 2) == '# ') list($title, $post) = preg_split('/\n/', $post, 2);
				preg_match_all(TAG_REGEX, $post, $channels);

				foreach($channels[3] as $channel_name)
				{
					mysql\query("INSERT INTO channels (name, user_id, post_id, created_at, private) VALUES ('%s', 1, %d, '%s', %d)", array($channel_name, $post_id, $now, $is_private));
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

		return app\response_302(BASE_URL);

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
				array('assertion'=>$req['form']['assertion'], 'audience'=>'http://127.0.0.1:80')
			);

			if ('okay' == $response['status'])
			{
				$_SESSION['persona'] = $response;

				$row = mysql\row('SELECT email FROM users where id = 1');
				if (empty($row))
				{
					mysql\query("INSERT INTO users (email, created_at) VALUES ('%s', NOW())", array($response['email']));
					if (mysql\affected_rows() === 1) $_SESSION['user'] = $response;
				}
				elseif ($row['email'] == $response['email']) $_SESSION['user'] = $response;
			}
		}
	});

?>