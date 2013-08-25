<?php
//TODO: split this into webmention and rssb?
	namespace webmention_response;


	function source_links_to_target($source_body, $target)
	{
		$dom = new \DOMDocument;
		@$dom->loadHTML($source_body);
		$links = $dom->getElementsByTagName('a');
		foreach ($links as $link)
		{
			$link_href = $link->getAttribute('href');
			if ($target == $link_href) return true;
		}

		return false;
	}


	function get_mf2($source_body, $url)
	{
		$mf2parser = new \mf2\Parser($source_body);
		return $mf2parser->parse();
	}


	function get_h_entry($mf2)
	{
		foreach ($mf2['items'] as $item)
		{
			if (in_array('h-entry', $item['type']))
			{
				return $item;
			}
		}
	}


	function get_h_card($h_entry_props, $mf2, $url)
	{
		// <div class="h-entry"><div class="p-author">John Doe</div> =>  author[0]='John Doe';
		// <div class="h-entry"><div class="p-author h-card">John Doe</div> => author[0][type][0]=h-card, author[0][properties][name][0]='John Doe'
		// <div class="h-entry"><div class="h-card"> => children[0][type][0] = h-card, children[0][properties][name][0] = John Doe

		if (isset($h_entry_props['author']) and is_array($h_entry_props['author']))
		{
			foreach ($h_entry_props['author'] as $author)
			{
				if (isset($author['type']) and is_array($author['type']) and in_array('h-card', $author['type']))
				{
//https://rawgithub.com/sandeepshetty/authorship-test-cases/master/h-entry_with_p-author.html
					return $author;
				}
			}
		}
		elseif (isset($mf2['rels']) and is_array($mf2['rels']) and isset($mf2['rels']['author']) and is_array($mf2['rels']['author']))
		{
//https://rawgithub.com/sandeepshetty/authorship-test-cases/master/h-entry_with_rel-author_pointing_to_h-card_with_u-url_equal_to_u-uid_equal_to_self.html
			$rel_author_absolute_url = trim(url_to_absolute($url, $mf2['rels']['author'][0]));
			$response_body = http\request("GET $rel_author_absolute_url", array(), array(), $response_headers);
			$rel_author_mf2 = get_mf2($response_body, $rel_author_absolute_url);
			$h_cards = get_top_level_h_cards($rel_author_mf2);

			if ($h_cards)
			{
				foreach ($h_cards as $h_card)
				{
					if (isset($h_card['properties']['url']) and isset($h_card['properties']['uid']))
					{
						$u_uid = trim(url_to_absolute($rel_author_absolute_url, $h_card['properties']['uid'][0]));
						foreach ($h_card['properties']['url'] as $h_card_u_url)
						{
							$u_url = trim(url_to_absolute($rel_author_absolute_url, $h_card_u_url));
							if (($u_url == $u_uid) and ($u_uid == $rel_author_absolute_url)) return $h_card;
						}
					}
				}

	//https://rawgithub.com/sandeepshetty/authorship-test-cases/master/h-entry_with_rel-author_pointing_to_h-card_with_u-url_that_is_also_rel-me.html
				foreach ($h_cards as $h_card)
				{
					if (isset($rel_author_mf2['rels']) and isset($rel_author_mf2['rels']['me']) and isset($h_card['properties']['url']))
					{
						$rel_mes = array_map(function ($rel_me) use ($rel_author_absolute_url) {
							return trim(url_to_absolute($rel_author_absolute_url, $rel_me));
						}, $rel_author_mf2['rels']['me']);

						$u_urls = array_map(function ($u_url) use ($rel_author_absolute_url) {
							return trim(url_to_absolute($rel_author_absolute_url, $u_url));
						}, $h_card['properties']['url']);

						if (array_intersect($rel_mes, $u_urls)) return $h_card;
					}
				}
			}
			else
			{
//https://rawgithub.com/sandeepshetty/authorship-test-cases/master/h-entry_with_rel-author_and_h-card_with_u-url_pointing_to_rel-author_href.html
				$h_cards = get_top_level_h_cards($mf2);

				foreach ($h_cards as $h_card)
				{
					if (isset($h_card['properties']['url']))
					{
						foreach ($h_card['properties']['url'] as $h_card_u_url)
						{
							$u_url = trim(url_to_absolute($rel_author_absolute_url, $h_card_u_url));
							if (($u_url == $rel_author_absolute_url)) return $h_card;
						}
					}
				}
			}
		}

	}

	function get_top_level_h_cards($mf2)
	{
		$h_cards = array();

		foreach ($mf2['items'] as $item)
		{
			if (in_array('h-card', $item['type']))
			{
				$h_cards[] = $item;
			}
		}

		return $h_cards;
	}

	function get_mention_type($h_entry_props, $target)
	{
		if (isset($h_entry_props['repost']) and in_array($target, $h_entry_props['repost'])) return 'repost';
		if (isset($h_entry_props['like']) and in_array($target, $h_entry_props['like'])) return 'like';
		if (isset($h_entry_props['in-reply-to']) and in_array($target, $h_entry_props['in-reply-to'])) return 'in-reply-to';
		return 'mention';
	}


	function get_mf2_data($response_body, $source, $target)
	{
		$mf2parser = new \mf2\Parser($response_body);
		$mf2 = $mf2parser->parse();
		$hcards = $hentry = $hentry_hcard = array();
		foreach ($mf2['items'] as $item)
		{
			if (in_array('h-card', $item['type'])) $hcards[] = $item;

			if (in_array('h-entry', $item['type']) and empty($hentry))
			{

				if (isset($item['properties']['repost']) and in_array($target, $item['properties']['repost'])) $hentry['type'] = 'repost';
				elseif (isset($item['properties']['like']) and in_array($target, $item['properties']['like'])) $hentry['type'] = 'like';
				elseif (isset($item['properties']['in-reply-to']) and in_array($target, $item['properties']['in-reply-to'])) $hentry['type'] = 'in-reply-to';
				else $hentry['type'] = 'mention';

				if (isset($item['properties']['summary'])) $hentry['content'] = $item['properties']['summary'][0];
				elseif (isset($item['properties']['name'])) $hentry['content'] = $item['properties']['name'][0];

				if (isset($item['properties']['published'])) $hentry['published'] = $item['properties']['published'][0];
				if (isset($item['properties']['author'])) $hentry_hcard = $item['properties']['author'][0];

				if (isset($item['properties']['url'])) $hentry['url'] = $item['properties']['url'];
			}
		}

		if (empty($hentry_hcard) and isset($hcards[0]))
		{
			$hentry_hcard = $hcards[0];
		}

		if (!isset($hentry['url'])) $hentry['url'] = $source;
		if (isset($hentry_hcard['properties']['name'])) $hentry['author']['name'] = $hentry_hcard['properties']['name'][0];
		if (isset($hentry_hcard['properties']['photo'])) $hentry['author']['photo'] = $hentry_hcard['properties']['photo'][0];
		if (isset($hentry_hcard['properties']['url'])) $hentry['author']['url'] = $hentry_hcard['properties']['url'][0];

		return $hentry;
	}

?>