<div class="media repost">
	<a class="pull-left h-card" href="<?php echo $repost['author_url'] ?>">
	<img alt="<?php echo $repost['author'] ?>" src="<?php echo $repost['author_photo'] ?>" class="media-object img-polaroid" width="42" title="<?php echo $repost['author'] ?>" />
	</a>
	<div class="media-body">
		<div class="post-content"><?php echo Markdown($repost['content']) ?></div>
		<div class="post-actions">
			<a class="" href="<?php echo $repost['url'] ?>"><time class="" datetime=""><?php echo $repost['published'] ?></time></a>
		</div>
	</div>
</div>