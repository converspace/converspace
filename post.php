<?php

	require __DIR__.'/vendor/phpish/http/http.php';

	use phpish\app;
	use phpish\template;
	use phpish\http;


	app\any('.*', function($req) {

		session_start();
		return app\next($req);
	});


	app\get('/post', function() {

		return template\compose('post.html', 'layout.html');
	});


	app\post('/post', function() {

		# Validate user email
		# Create new post
	});



	app\post('/signout', function() {

		unset($_SESSION['user']);
		session_destroy();
	});


	app\post('/persona-verifier', function($req) {

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