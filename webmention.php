<?php

	use phpish\http;
	use mf2\Parser;


	function discover_webmention_endpoint($target)
	{
		$response_body = http\request("GET $target", array(), array(), $response_headers);
		if (isset($response_headers['link']) and preg_match('#<(https?://[^>]+)>; rel="http://webmention.org/"#', $response_headers['link'], $matches))
		{
			return $matches[1];
		}
		elseif (preg_match('#<link href="([^"]+)" rel="http://webmention.org/" ?/?>#i', $response_body, $matches) or preg_match('#<link rel="http://webmention.org/" href="([^"]+)" ?/?>#i', $response_body, $matches))
		{
			return $matches[1];
		}
	}


	function send_webmention($source, $target)
	{
		if ($target_webmention_endpoint = discover_webmention_endpoint($target))
		{
			$response_body = http\request("POST $target_webmention_endpoint", array(), array('source'=>$source, 'target'=>$target), $response_headers);
			return array('headers'=>$response_headers, 'body'=>$response_body);
		}
	}


	function get_webmention_data($response_body, $source)
	{
		$mf2parser = new Parser($response_body);
		$mf2 = $mf2parser->parse();
		$hcards = $hentry = $hentry_hcard = array();
		foreach ($mf2['items'] as $item)
		{
			if (in_array('h-card', $item['type'])) $hcards[] = $item;

			if (in_array('h-entry', $item['type']) and empty($hentry))
			{

				if (isset($item['properties']['repost'])) $hentry['type'] = 'repost';
				elseif (isset($item['properties']['like'])) $hentry['type'] = 'like';
				elseif (isset($item['properties']['in-reply-to'])) $hentry['type'] = 'in-reply-to';
				else $hentry['type'] = 'mention';

				if (isset($item['properties']['summary'])) $hentry['content'] = $item['properties']['summary'][0];
				elseif (isset($item['properties']['name'])) $hentry['content'] = $item['properties']['name'][0];

				if (isset($item['properties']['published'])) $hentry['published'] = $item['properties']['published'];
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


	function source_links_to_target($source_content, $target)
	{
		$dom = new DOMDocument;
		@$dom->loadHTML($source_content);
		$links = $dom->getElementsByTagName('a');
		foreach ($links as $link)
		{
			$link_href = $link->getAttribute('href');
			if ($target == $link_href) return true;
		}

		return false;
	}

?>