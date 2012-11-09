<!DOCTYPE html>
<html>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo SITE_TITLE ?></title>

	<link href="<?php echo SITE_BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo SITE_BASE_URL ?>assets/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="<?php echo SITE_BASE_URL ?>assets/css/persona-buttons.css" rel="stylesheet">
	<style>


		#stream-container { margin-bottom: 20px; }
		#stream-header { border-bottom: 1px solid #eee; }
		.author { padding: 30px 20px; }
		.author-name { font-size: 30px; font-weight: 900; line-height: 30px; letter-spacing: -1px; }
		.author p { font-size: 18px; font-weight: 200; line-height: 25px;; color: #999; }

		.sidebar { background-color: #FEFEFE; margin-top: 30px; }

		.post, .post-form { padding: 20px 20px; border-bottom: 1px solid #eee; font-size: 18px; line-height: 1.5; }
		.post:first-child { margin-top: 10px; }
		.post:last-child { border-bottom: 0 none; }
		.post-permalink { font-size: 10px; color: #999;}
		.post-permalink a { color: #999;}
		.post h1 { font-weight: 200; font-size: 28px; }

		.infobox { padding: 30px 20px 0 20px; }

		.hash { color: #08C; opacity: 0.6; font-weight: lighter ;}
		.channels { margin-bottom: 0; }
		.channels a { text-decoration: none; }
		.channel { display: block; padding: 8px 14px; text-decoration: none; }
		.channel .icon-tag, .channel .icon-home { opacity: 0.25; }
		.channel:hover, .channel:hover .hash { background-color: #F5F5F5; color: #005580; }
		.channel:hover .icon-tag { opacity: 0.5; }
		.channels .active, .channels .active:hover { background-color: #08C; color: #FFF;}
		.channels .active .hash, .channels .active:hover .hash { background-color: #08C; color: #FFF;}
		.channels .active .icon-tag, .channels .active .icon-home, .channels .active:hover .icon-chevron-right { opacity: 1; }

		.persona-button { margin-top: 7px; }

	</style>

</head>
<body>

<div class="navbar navbar-static-top">
	<div class="navbar-inner">
		<div class="container">

		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

			<a class="brand" href="<?php echo SITE_BASE_URL ?>"><?php echo SITE_TITLE ?></a>

<div class="nav-collapse collapse">

			<?php if (isset($_SESSION['persona'])) : ?>

				<?php if (isset($_SESSION['user'])) : ?>
				<ul class="nav">
					<li class="active"><a href="<?php echo SITE_BASE_URL ?>">Public</a></li>
					<li><a href="#private">Private</a></li>
				</ul>
				<?php endif; ?>


				<script>
					var $loggedInUser = "<?php echo $_SESSION['persona']['email'] ?>";
				</script>
				<!-- a id="signout" href="#" class="pull-right"><span>Sign out <?php echo $_SESSION['persona']['email'] ?></span></a -->
				<ul class="nav pull-right">
				<li><a href="#" id="signout">Logout</a></li>
				</ul>

			<?php else: ?>
				<script>
					var $loggedInUser = null;
				</script>
				<!-- a id="signin" href="#" class="pull-right"><span>Sign in with Mozilla Persona</span></a -->
				<ul class="nav pull-right">
				<li><a href="#" id="signin">Login</a></li>
				</ul>

			<?php endif; ?>
</div>
		</div>
	</div>
</div>


<div class="container" id="stream-container">

	<div class="row">
		<div class="span12" id="stream-header">
			<div class="row">
				<div class="span9">
					<div class="author">
						<div class="media">
							<a class="pull-left" href="<?php echo SITE_BASE_URL ?>channels/about">
								<?php echo gravatar(USER_EMAIL, 420, 'mm', 'g', true, array('class'=>'media-object img-polaroid', "width"=>80)) ?>
							</a>
							<div class="media-body">
								<h1 class="author-name media-heading"><?php echo USER_NAME ?></h1>
								<?php echo Markdown(USER_BIO) ?>
							</div>
						</div>
					</div>
				</div>
				<div class="span3">
					<!-- TODO: Links to Twitter / Facebook / LinkedIn? -->
				</div>
			</div>
		</div>
	</div>

<?php echo $content; ?>
</div>

<script src="https://login.persona.org/include.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/jquery-1.8.2.min.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/bootstrap.min.js"></script>

<script>

$(document).ready(function() {

	$('#signin').click(function (e) {
		e.preventDefault();
		// TODO add returnTo: '/pathToReturnTo.html',
		navigator.id.request({siteName: 'Converspace'});
	});

	$('#signout').click(function (e) {
		e.preventDefault();
		navigator.id.logout();
	});

	navigator.id.watch({
		loggedInUser: $loggedInUser,
		onlogin: function ($assertion) {
			$.post(
				'<?php echo SITE_BASE_URL ?>persona-verifier',
				{assertion: $assertion},
				function(data) {
					window.location.reload();
				}
			);
		},
		onlogout: function () {
			$.post(
				'<?php echo SITE_BASE_URL ?>signout',
				{},
				function() {
					window.location.reload();
				}
			);
		}
	});

});

</script>
</body>
</html>
