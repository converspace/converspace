<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
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
    <link href="http://fonts.googleapis.com/css?family=Merriweather:300,400" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700' rel='stylesheet' type='text/css' />


	<?php if (isset($individual_post)) : ?>
	<link href="<?php echo SITE_BASE_URL.'webmention' ?>" rel="http://webmention.org/" />
	<?php endif; ?>

</head>
<body>

	<!-- Primary Page Layout
	================================================== -->
	<div id="nav">
		<div class="container">
			<div class="twelve columns offset-by-two clearfix">
				<?php $channels = db_get_channels($authorized); ?>
				<?php foreach ($channels as $channel): ?>
					<a class="foobar" href=""><span class="deem">#</span><?php echo $channel['name'] ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

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



			<?php if ($alert = session_alert()) : ?>
			<div class="infobox">
			<div class="alert alert-<?php echo $alert['type'] ?>">
				<?php echo $alert['msg'] ?>
			</div>
			</div>
			<?php endif; ?>


			<?php if (isset($_SESSION['user'])) : ?>
			<div>
				<div class="post-box">
					<form method="post" action="<?php echo SITE_BASE_URL ?>post">
					<?php if (isset($individual_post)) : ?>
						<textarea rows="4" name="post[content]" placeholder="What's on your mind?"><?php if (isset($individual_post)) echo $posts[0]['raw'] ?></textarea>
						<input type="hidden" name="post[id]" value="<?php echo $posts[0]['id'] ?>">
						<div><button type="submit" >Update</button></div>
					<?php else: ?>
						<textarea rows="4" name="post[content]" placeholder="What's on your mind?"></textarea>
						<div><button type="submit" >Post</button></div>
					<?php endif; ?>
					</form>
				</div>
			</div>
			<?php endif; ?>


			<?php if ($posts) : ?>
			<?php if (!empty($channel_name)): ?>
			<div class="infobox" >
			<div class="alert alert-info">
			Showing posts from the #<strong><?php echo $channel_name ?></strong> channel. <a href="<?php echo SITE_BASE_URL ?>">Show all posts</a>
			</div>
			</div>
			<?php endif; ?>
			<?php endif; ?>



			<?php if ($posts) : ?>
			<?php foreach ($posts as $post): ?>

			<div class="h-entry">
				<div class="post-box">
					<div class="e-content">
						<?php echo $post['content'] ?>
					</div>
					<div class="post-footer">
						<a class="u-url" title="Permalink" href="<?php echo SITE_BASE_URL.$post['id'] ?>"><time class="dt-published" datetime="<?php echo date(DATE_ATOM, strtotime($post['created_at'])); ?>"><?php echo date('j M Y', strtotime($post['created_at'])); ?></time></a>
					</div>
				</div>
			</div>

			<?php endforeach; ?>
			<?php endif; ?>

		</div>
	</div>

	<?php if ($posts) : ?>
	<div class="container pager">
		<div class="twelve columns offset-by-two">
			<?php if (!isset($individual_post) and isset($pager['after'])): ?>
			<a class="newer" href="?after=<?php echo $pager['after'] ?>">&larr; Newer</a>
			<?php elseif (isset($pager['after'])): ?>
			<a class="newer" href="<?php echo SITE_BASE_URL.$pager['after'] ?>">&larr; Newer</a>
			<?php endif; ?>
			<?php if (!isset($individual_post) and isset($pager['before'])): ?>
			<a class="older" href="?before=<?php echo $pager['before'] ?>">Older &rarr;</a>
			<?php elseif (isset($pager['before'])): ?>
			<a class="older" href="<?php echo SITE_BASE_URL.$pager['before'] ?>">Older &rarr;</a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>


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