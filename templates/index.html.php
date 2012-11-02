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
						<?php echo gravatar($user['email'], 420, 'mm', 'g', true, array('class'=>'media-object img-polaroid', "width"=>80)) ?>
					</a>
					<div class="media-body">
						<h1 class="author-name media-heading"><?php echo isset($user['name']) ? $user['name'] : '' ?></h1>
						<?php echo isset($user['bio']) ? Markdown($user['bio']) : ''?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span9 content">

			<?php foreach ($posts as $post): ?>
			<div class="post">
				<?php echo $post['content'] ?>
				<div class="post-permalink"><a href="posts/<?php echo $post['id'] ?>"><?php echo $post['created_at'] ?></a></div>
			</div>
			<?php endforeach; ?>

		</div>

		<div class="span3 sidebar">
		<!--
			<div class="about">
				<?php echo gravatar($user['email'], 420, 'mm', 'g', true, array('class'=>'img-polaroid', "width"=>180)) ?>
					<h3 class="author-name"><?php echo isset($user['name']) ? $user['name'] : '' ?></h3>
					<p class="author-bio"><?php echo isset($user['bio']) ? $user['bio'] : ''?></p>
			</div>
		-->
			<ul class="unstyled channels ">
				<li><a href="#" class="channel active"><i class="icon-chevron-left icon-white"></i> Home</a></li>
				<?php foreach ($channels as $channel): ?>
				<li><a class="channel" href="channels/<?php echo $channel['name'] ?>"><i class="icon-chevron-left"></i> <span class="hash">#</span><?php echo $channel['name'] ?></a></li>

				<?php endforeach; ?>
			</ul>

		</div>

	</div>