<?php use \Michelf\MarkdownExtra; ?>
<div class="media repost">
	<a class="pull-left h-card" href="<?php echo $machinetags[$mention['type']]['author_url'] ?>">
	<img alt="<?php echo $machinetags[$mention['type']]['author_name'] ?>" src="<?php echo $machinetags[$mention['type']]['author_photo'] ?>" class="media-object img-polaroid" width="42" title="<?php echo $machinetags[$mention['type']]['author_name'] ?>" />
	</a>
	<div class="media-body">
		<div class="post-content"><?php echo MarkdownExtra::defaultTransform($machinetags[$mention['type']]['content']) ?></div>
		<div class="post-actions">
			<a class="<?php echo $mention['class'] ?>" href="<?php echo $machinetags[$mention['type']]['url'] ?>"><time class="" datetime=""><?php echo $machinetags[$mention['type']]['published'] ?></time></a>
		</div>
	</div>
</div>