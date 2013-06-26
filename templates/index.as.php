<?php
	$h_entry = $mf2['items'][1]['properties'];
	$h_card = $mf2['items'][0]['properties'];
	$verb = 'post'; $in_reply_to = false;
	foreach ($mf2['items'] as $item)
	{
		if (in_array('h-entry', $item['type']))
		{
			if (isset($item['properties']['repost'])) $verb = 'repost';
			if (isset($item['properties']['like'])) $verb = 'like';
			if (isset($item['properties']['in-reply-to'])) $in_reply_to = true;
		}
	}
?>{
    "actor": {
        "url": "<?php echo $h_card['url'][0] ?>",
        "objectType" : "person",
        "displayName": "<?php echo $h_card['name'][0] ?>",
        "image": {
            "url": "<?php echo $h_card['photo'][0] ?>"
        }
    },
    "verb": "<?php echo $verb; ?>",
    "object": {
	<?php if (('like' == $verb) or ('repost' == $verb)): ?>

		"url": "<?php echo $h_entry[$verb][0]; ?>"

	<?php elseif ($in_reply_to): ?>

		"objectType": "comment",
        "content": "<?php echo $h_entry['content'][0]; ?>"
        "inReplyTo": [
            {
                "url": "<?php echo $h_entry['in-reply-to'][0]; ?>"
            }
        ]

	<?php else: ?>

		"url": "<?php echo $h_entry['url'][0]; ?>",
		"content": "<?php echo $h_entry['content'][0]; ?>"

	<?php endif; ?>
    },
	"published": "<?php echo $h_entry['published'][0]; ?>"

}