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
		<div class="span3">
			<div class="about">
				<!--<img src="assets/img/222x222.gif" class="img-polaroid">-->
				<a href="#about"><?php echo gravatar($email, 420, 'mm', 'g', true, array('class'=>'img-polaroid', 'height'=>222, "width"=>222)) ?></a>
				<div class="caption">
					<h3>Author Name</h3>
					<p>A few words about the Author</p>
				</div>
				<ul class="unstyled">
					<li><a href="#">Twitter</a></li>
					<li><a href="#">Facebook</a></li>
					<li><a href="#">Google+</a></li>
					<li><a href="#">Github</a></li>
					<li><a href="#">Contact Form</a></li>
				</ul>
			</div>
		</div>
		<div class="span7">
            <div class="tab-content">
                <div id="stream" class="tab-pane active">

					<?php foreach ($posts as $post): ?>
					<div>
						<!--
						<?php if (!empty($post['title'])): ?>
						<p>
							<h3><a href="#posts/<?php echo $post['id'] ?>"><?php echo $post['title'] ?></a></h3>
						</p>
						<?php endif; ?>
						-->
						<?php echo $post['content'] ?>
						<p class=""><i class="icon-time"></i>  <small><a href="#posts/<?php echo $post['id'] ?>"><?php echo $post['created_at'] ?></a></small></p>
					</div>

					<hr>
					<?php endforeach; ?>

				</div>
            </div>
		</div>

		<div class="span2">
			<div class="Channels">
				<div class="caption">
					<h3>Channels</h3>
					<ul class="unstyled">
						<li><a href="#">All</a></li>
						<!--<li><a href="#">Drafts</a> (only visible to logged in user)</li>-->
						<?php foreach ($channels as $channel): ?>
						<li><a href="#channels/<?php echo $channel['name'] ?>"><?php echo $channel['name'] ?> (<?php echo $channel['count'] ?>)</a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

	</div>