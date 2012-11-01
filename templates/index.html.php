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
	<div class="row">
		<div class="span3 sidebar">
			<div class="about">
				<?php echo gravatar($email, 420, 'mm', 'g', true, array('class'=>'img-polaroid', "width"=>175)) ?>
					<h3 class="author-name"><?php echo $name ?></h3>
					<p class="author-bio">Short author bio goes here</p>
			</div>

			<ul class="unstyled channels">
				<li><a href="#" class="active channel">Home (All Channels) <i class="icon-chevron-right icon-white pull-right"></i></a></li>
				<!--<li><a href="#">Drafts</a> (only visible to logged in user)</li>-->
				<?php foreach ($channels as $channel): ?>
				<li><a class="channel" href="channels/<?php echo $channel['name'] ?>"><span class="hash">#</span><?php echo $channel['name'] ?> <i class="icon-chevron-right pull-right"></i></a></li>

				<?php endforeach; ?>
			</ul>

		</div>
		<div class="span9 content">

			<?php foreach ($posts as $post): ?>
			<div class="post">
				<?php echo $post['content'] ?>
				<div class="post-permalink post-time muted"><a href="posts/<?php echo $post['id'] ?>"><?php echo $post['created_at'] ?></a></div>
			</div>
			<?php endforeach; ?>

		</div>

	</div>