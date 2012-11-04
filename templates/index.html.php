<?php

function gravatar($email, $s=80, $d='mm', $r='g', $img=false, $atts=array())
{
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5(strtolower(trim($email)));
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img )
	{
		$url = "<img src=\"$url\"";
		foreach ($atts as $key=>$val) $url .= " $key=\"$val\"";
		$url .= ' />';
	}

	return $url;
}


?>
	<div class="row" id="stream-header">
		<div class="span12">
			<div class="author">
				<div class="media">
					<a class="pull-left" href="">
						<?php echo gravatar(USER_EMAIL, 420, 'mm', 'g', true, array('class'=>'media-object img-polaroid', "width"=>80)) ?>
					</a>
					<div class="media-body">
						<h1 class="author-name media-heading"><?php echo USER_NAME ?></h1>
						<?php echo Markdown(USER_BIO) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span9 content">

			<?php if (isset($_SESSION['user'])) : ?>
			<div class="post-form">

				<?php if (!empty($_SESSION['alert'])) : ?>
				<div style="padding: 5px 0;">
				<div class="alert alert-<?php echo $_SESSION['alert']['type'] ?>">
					<button type="button" class="close" data-dismiss="alert">x</button>
					<?php echo $_SESSION['alert']['msg'] ?>
				</div>
				</div>
				<?php unset($_SESSION['alert']) ?>
				<?php endif; ?>

				<form method="post" action="post">
					<textarea class="span9" rows="4" name="post" placeholder="What's on your mind?"></textarea>
					<label class="checkbox inline"><input type="checkbox" name="private" value="1"> Private</label>
					<button type="submit" class="btn pull-right">Post</button>
				</form>
			</div>
			<?php endif; ?>


			<?php foreach ($posts as $post): ?>
			<div class="post">
				<?php echo $post['content'] ?>
				<div class="post-permalink"><a href="posts/<?php echo $post['id'] ?>"><?php echo $post['created_at'] ?></a></div>
			</div>
			<?php endforeach; ?>

		</div>

		<div class="span3 sidebar">

			<ul class="unstyled channels ">
				<li><a href="#" class="channel active"><i class="icon-chevron-left icon-white"></i> Home</a></li>
				<?php foreach ($channels as $channel): ?>
				<li><a class="channel" href="channels/<?php echo $channel['name'] ?>"><i class="icon-chevron-left"></i> <span class="hash">#</span><?php echo $channel['name'] ?></a></li>

				<?php endforeach; ?>
			</ul>

		</div>

	</div>