<?php

	require __DIR__.'/vendor/phpish/app/app.php';
	require __DIR__.'/vendor/phpish/template/template.php';
	require __DIR__.'/vendor/phpish/http/http.php';

	use phpish\app;
	use phpish\template;
	use phpish\http;


	app\get('/', function() {

		return template\compose('index.html', array(), 'layout.html');
	});


	app\get('/signin', function() {

		session_start();
		return template\compose('signin.html', array(), 'layout.html');
	});


	app\post('/signout', function() {

		session_start();
		unset($_SESSION['user']);
		session_destroy();
	});


	app\post('/persona-verifier', function($req) {

		session_start();
		if (isset($req['form']['assertion'])) {
			$response = http\request(
				"POST https://verifier.login.persona.org/verify",
				'',
				array('assertion'=>$req['form']['assertion'], 'audience'=>'http://127.0.0.1:80')
			);

			if ('okay' == $response['status']) $_SESSION['user'] = $response;
		}
	});


	app\get('/post', function() {

		# temp interface for posting
	});


	app\post('/post', function() {

		# temp interface for posting
	});


# TODO: look at AtomPub?

	app\get('/posts/[{id}]', function() {

		# get post from db
		# convert hashtags into links
		# apply markdown
	});

	app\get('/channels/[{id}]', function() {

		# /channels/ is ordered by count of posts in the channel. Used by the channel sidebar.
	});

?>