<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title><?php echo SITE_TITLE ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL ?>css/base.css">
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL ?>css/skeleton.css">
	<link rel="stylesheet" href="<?php echo SITE_BASE_URL ?>css/layout.css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicons
	================================================== -->
	<!-- link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png" -->


	<!--WEB FONTS-->
    <link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" />
    <!-- link href="http://fonts.googleapis.com/css?family=Merriweather:300,400" rel="stylesheet" / -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300' rel='stylesheet' type='text/css' />
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

	<?php if (isset($individual_post)) : ?>
	<link href="<?php echo SITE_BASE_URL.'webmention' ?>" rel="http://webmention.org/" />
	<link rel="pingback" href="http://pingback.me/webmention?forward=<?php echo SITE_BASE_URL.'webmention' ?>" />
	<?php endif; ?>

</head>
<body>

	<!-- Primary Page Layout
	================================================== -->

	<div id="header">
		<?php if (isset($_SESSION['persona'])) : ?>
		<div class="float-menu">
			<script> var $loggedInUser = "<?php echo $_SESSION['persona']['email'] ?>"; </script>
			<a href="#" id="signout">Logout</a>
		</div>
		<?php else: ?>
		<div class="float-menu">
			<script> var $loggedInUser = null; </script>
			<a href="#" id="signin">Login</a>
		</div>
		<?php endif; ?>


		<div class="container">
			<div class="h-card twelve columns offset-by-two">
				<a class="u-url" href="<?php echo SITE_BASE_URL ?>">
					<img class="u-photo" src="<?php echo gravatar_url(USER_EMAIL, 420, 'mm', 'g', true) ?>" width="80">
				</a>
				<p class="p-name"><?php echo USER_NAME ?></p>
				<p class="bio p-note"><?php echo USER_BIO ?></p>
			</div>
		</div>
	</div>

	<div class="section container">
		<div id="content" class="twelve columns offset-by-two">

			<?php echo $content ?>

		</div>
	</div>

	<?php if ($posts) : ?>
	<div class="container pager">
		<div class="twelve columns offset-by-two">
			<?php if (!isset($individual_post) and isset($pager['after'])): ?>
			<a rel="prev" class="newer" href="?after=<?php echo $pager['after'] ?>">&larr; Newer</a>
			<?php elseif (isset($pager['after'])): ?>
			<a rel="prev" class="newer" href="<?php echo SITE_BASE_URL.$pager['after'] ?>">&larr; Newer</a>
			<?php endif; ?>
			<?php if (!isset($individual_post) and isset($pager['before'])): ?>
			<a rel="next" class="older" href="?before=<?php echo $pager['before'] ?>">Older &rarr;</a>
			<?php elseif (isset($pager['before'])): ?>
			<a rel="next" class="older" href="<?php echo SITE_BASE_URL.$pager['before'] ?>">Older &rarr;</a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>


	<div id="nav">
		<div class="container">
			<div class="twelve columns offset-by-two">
				<div class="tags">
				<?php $channels = db_get_channels($authorized); ?>
				<?php foreach ($channels as $channel): ?>
					<a class="tag" rel="tag" href="<?php echo SITE_BASE_URL.$channel['name'].'/' ?>"><span class="deem">#</span><?php echo $channel['name'] ?></a>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>


<!-- End Document
================================================== -->
<script src="https://login.persona.org/include.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/jquery-1.8.2.min.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/jquery.fitvids.js"></script>

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

	$(".e-content").fitVids();

});

</script>

</body>
</html>