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

		html, body {min-height: 100%;}
		.container { min-height: 100%; background: url(assets/img/separator.png) repeat-y 220px top;}

		.about {margin-bottom: 30px;}
		.author-name { letter-spacing: -1px; margin-bottom: 8px; line-height: 1em;}
		body {color: #5A5A5A;} /* #495961 */
		body {color: #333;}
		h1, h2, h3, h4, h5, h6 {color: #5A5A5A;}
		author-bio {color: #666;}

		.sidebar { padding: 30px 0;} /* border-right: 1px solid #eee; */
		.content {margin-left: 0; padding: 30px 0;}
		.post {padding: 30px; border-bottom: 1px solid #eee; font-size: 1.4em;}
		.post:first-child, .post:first-child h1 {margin-top: 0; padding-top: 0;}
		.post:last-child {border-bottom: 0 none;}
		.post-permalink a { font-size: 0.6em; color: #999;}

		.channels { border-top: 1px solid #EEE; margin-bottom: 0;}

		.channels li {padding: 0; margin: 0;}
		.channels a {text-decoration: none;}
		.channel {display: block; padding: 8px 14px; text-decoration: none;}
		.channel .icon-chevron-right {opacity: 0.25;}
		.channels .active, .channels .active:hover {background-color: #08C; color: white;}
		.channels .active .icon-chevron-right, .channels .active:hover .icon-chevron-right {opacity: 1;}
		.channel:hover {background-color: whiteSmoke; color: #005580;}
		.channel:hover .icon-chevron-right {opacity: 0.5;}
		.hash {color: #08C; opacity: 0.6; font-weight: lighter;}

	</style>

</head>
<body>
<div class="container">
<?php echo $content; ?>
</div>

<script src="assets/js/jquery-1.8.2.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/persona.js"></script>
</body>
</html>
