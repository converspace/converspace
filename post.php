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

		$template_vars = !isset($_SESSION['user']) ? array('alert'=>'Not signed in! Please sign in first.', 'alert_type'=>'error') : array();
		return template\compose('post.html', $template_vars, 'layout.html');
	});


	app\post('/post', function() {

		$template_vars = array();

		if (!isset($_SESSION['user']))
		{
			$template_vars['alert'] = 'Not signed in! Please sign in first.';
			$template_vars['alert_type'] = 'error';
		}
		else
		{
			$template_vars['alert'] = 'Sorry! Not yet implemented. Coming soon...';
			$template_vars['alert_type'] = 'info';
		}

		return template\compose('post.html', $template_vars, 'layout.html');
		# Validate user email
		# Create new post
	});



	app\post('/signout', function() {

		unset($_SESSION['user']);
		session_destroy();
	});


	app\post('/persona-verifier', function($req) {

# If users.id = 1 does not exist create it.
# Only login user if users.id = 1

		if (isset($req['form']['assertion'])) {
			$response = http\request(
				"POST https://verifier.login.persona.org/verify",
				'',
				array('assertion'=>$req['form']['assertion'], 'audience'=>'http://127.0.0.1:80')
			);

			if ('okay' == $response['status']) $_SESSION['user'] = $response;
		}
	});

?>