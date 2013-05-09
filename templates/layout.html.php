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


		body { padding: 0 20px; }
		.navbar { margin: 0 -20px; }
		.navbar-inner { -webkit-box-shadow: none; -moz-box-shadow: none; box-shadow: none; }
		#stream-container { margin-bottom: 20px; }
		#stream-header { border-bottom: 1px solid #E5E5E5; }
		#stream-content { border-top: 1px solid white; padding-top: 30px;}
		.author { padding: 30px 0; }
		.author-name { font-size: 30px; font-weight: 900; line-height: 30px; letter-spacing: -1px; }
		.author p { font-size: 18px; font-weight: 200; line-height: 25px;; color: #999; }
		.infobox { margin-bottom: 30px; }

		.posts { border-bottom: 1px solid white; margin-bottom: 30px; }
		.post-form { border-bottom: 1px solid white; margin-bottom: 30px; }
		.post-form-inner { border-bottom: 1px solid #E5E5E5; padding-bottom: 10px; }
		.post { padding: 30px 0; border-bottom: 1px solid #E5E5E5; border-top: 1px solid white; font-size: 16px; }
		.post:first-child { border-top: none; padding-top: 0; }
		.post-permalink { font-size: 10px; color: #999;}
		.post-permalink a { color: #999;}
		.post h1 { font-weight: 200; font-size: 28px; margin-top: -5px;}

		.deem { color: #08C; opacity: 0.6; font-weight: lighter ;}
		.channels { margin-bottom: 0; background: none; }
		.channels a { text-decoration: none; }
		.channel { display: block; padding: 8px 14px; text-decoration: none; }
		.channel .icon-tag, .channel .icon-home { opacity: 0.25; }
		.channel:hover, .channel:hover .deem { background-color: #FFF; color: #005580; }
		.channel:hover .icon-tag { opacity: 0.5; }

		.channels .active, .channels .active:hover, .channels .active .deem, .channels .active:hover .deem { background-color: #FFF; color: #005580;}

		.fluid-width-video-wrapper { margin-bottom: 20px; }
		.pager { margin-top: 0px; margin-bottom: 30px; }

		.additional_tags  { font-size: 14px; color: #999; }
		.additional_tags a, .additional_tags .deem {  color: #999; }

	</style>

</head>
<body>


<div class="container" id="stream-container">

<div class="navbar " style="border-bottom: 1px solid white;">
	<div class="navbar-inner" style="-webkit-box-shadow: none; -moz-box-shadow: none; box-shadow: none; border: none; border-bottom: 1px solid #E5E5E5; background: none; ">
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

		<a class="brand" href="<?php echo SITE_BASE_URL ?>"><?php echo SITE_TITLE ?></a>

		<div class="nav-collapse collapse">

		<?php if (isset($_SESSION['persona'])) : ?>

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

	<div class="row" id="stream-header">
		<!-- div class="span12">
			<div class="row" -->
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
			<!-- /div>
		</div -->
	</div>

<?php echo $content; ?>
</div>

<script src="https://login.persona.org/include.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/jquery-1.8.2.min.js"></script>
<script src="<?php echo SITE_BASE_URL ?>assets/js/bootstrap.min.js"></script>
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

	$(".posts").fitVids();

});

</script>
</body>
</html>
