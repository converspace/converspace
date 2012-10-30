<?php

	require __DIR__.'/vendor/phpish/http/http.php';

	use phpish\app;
	use phpish\mysql;
	use phpish\template;
	use phpish\http;


	app\any('.*', function($req) {

		session_start();
		return app\next($req);
	});


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

		$template_vars = array();

		if (!isset($_SESSION['user']))
		{
			$template_vars['alert'] = 'You are not authorized to post.';
			$template_vars['alert_type'] = 'error';
		}
		else
		{
			$post = $req['form']['post'];
			$now = date('Y-m-d H:i:s');
			mysql\query("INSERT INTO posts (user_id, content, created_at, updated_at) VALUES (1, '%s', '%s', '%s')", array($post, $now, $now));
			if (mysql\affected_rows() === 1)
			{
				$post_id = mysql\insert_id();
				if (substr($post, 0, 2) == '# ') list($title, $post) = preg_split('/\n/', $post, 2);
				preg_match_all('/(?:^|\s)(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms', $post, $channels);

				foreach($channels[2] as $channel_name)
				{
					mysql\query("INSERT INTO channels (name, user_id, post_id, created_at) VALUES ('%s', 1, %d, '%s')", array($channel_name, $post_id, $now));
				}

				$template_vars['alert'] = 'Post Saved!';
				$template_vars['alert_type'] = 'success';
			}
			else
			{
				error_log('Error while saving post: '.mysql\error());
				$template_vars['alert'] = 'Sorry! Error while saving post! ';
				$template_vars['alert_type'] = 'error';
			}
		}

		return template\compose('post.html', $template_vars, 'layout.html');

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