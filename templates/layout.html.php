<!DOCTYPE html>
<html>
	<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Converspace</title>

	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/persona-buttons.css" rel="stylesheet">
	<script src="https://login.persona.org/include.js"></script>
	<style>

		html ,body, .stream-container { min-height: 100% }

		.stream-container { margin-bottom: 20px; background: url(assets/img/separator.png) repeat-y 720px top; }
		.sidebar { margin-left: 0; }
		.content { margin-left: 0; width: 740px; }
		#stream-header { padding: 30px 30px 30px 0; border-bottom: 1px solid #eee; background-color: #FFF; }
		.post, .post-form { padding: 20px; border-bottom: 1px solid #eee; font-size: 18px; line-height: 1.5; }
		.post:hover { background-color: #F5F5F5; }
		.post:last-child { border-bottom: 0 none; }
		.post-permalink a { font-size: 10px; color: #999;}
		.post h1 { font-weight: 200; font-size: 28px; }
		.author-name { font-size: 30px; font-weight: 900; line-height: 30px; letter-spacing: -1px; }
		.author p { font-size: 18px; font-weight: 200; line-height: 25px;; color: #999; }


		.hash { color: #08C; opacity: 0.6; font-weight: lighter ;}
		.channels { margin-bottom: 0; }
		.channels a { text-decoration: none; }
		.channel { display: block; padding: 8px 14px; text-decoration: none; }
		.channel .icon-chevron-left { opacity: 0.25; }
		.channel:hover, .channel:hover .hash { background-color: #F5F5F5; color: #005580; }
		.channel:hover .icon-chevron-left { opacity: 0.5; }
		.channels .active, .channels .active:hover { background-color: #08C; color: #FFF;}
		.channels .active .icon-chevron-left, .channels .active:hover .icon-chevron-right { opacity: 1; }

		.channel.first-child {-webkit-border-radius: 6px 6px 0 0;
-moz-border-radius: 6px 6px 0 0;
border-radius: 6px 6px 0 0;}

		.persona-button { margin-top: 7px; }

	</style>

</head>
<body>

<div class="navbar navbar-static-top">
	<div class="navbar-inner">
		<div class="container">
			<?php if (isset($_SESSION['persona'])) : ?>

				<?php if (isset($_SESSION['user'])) : ?>
				<ul class="nav">
					<li class="active"><a href="#">Public</a></li>
					<li><a href="#private">Private</a></li>
				</ul>
				<?php endif; ?>

				<script>
					var $loggedInUser = "<?php echo $_SESSION['persona']['email'] ?>";
				</script>
				<a id="signout" href="#" class="persona-button orange pull-right"><span>Sign out <?php echo $_SESSION['persona']['email'] ?></span></a>

			<?php else: ?>
				<script>
					var $loggedInUser = null;
				</script>
				<a id="signin" href="#" class="persona-button pull-right"><span>Sign in with Mozilla Persona</span></a>
			<?php endif; ?>
		</div>
	</div>
</div>


<div class="container stream-container">
<?php echo $content; ?>
</div>

<script src="assets/js/jquery-1.8.2.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/persona.js"></script>
</body>
</html>
