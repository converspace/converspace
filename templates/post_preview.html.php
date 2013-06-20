<?php use \Michelf\MarkdownExtra; ?>
<div class="post-preview">
	<?php echo MarkdownExtra::defaultTransform(@$machinetags[$mention['type']]['content']) ?>

	&#8212; <a class="p-name" href="<?php echo @$machinetags[$mention['type']]['author_url'] ?>"><?php echo @$machinetags[$mention['type']]['author_name'] ?></a> on <a class="permalink <?php echo $mention['class'] ?>" href="<?php echo $machinetags[$mention['type']]['url'] ?>"><time class="" datetime=""><?php echo date('j M Y', strtotime(@$machinetags[$mention['type']]['published'])); ?></time></a>
</div>