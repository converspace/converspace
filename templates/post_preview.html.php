<?php use \Michelf\MarkdownExtra; ?>
<blockquote>
	<?php echo MarkdownExtra::defaultTransform(@$machinetags[$mention['type']]['content']) ?>
</blockquote>