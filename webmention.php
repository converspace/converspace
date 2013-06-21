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


	function get_webmention_type($response_body)
	{
		$mf2parser = new Parser($response_body);
		$mf2 = $mf2parser->parse();
		$type = 'mention';
		foreach ($mf2['items'] as $item)
		{
			if (in_array('h-entry', $item['type']))
			{
				if (isset($item['properties']['repost'])) $type = 'repost';
				if (isset($item['properties']['like'])) $type = 'like';
				if (isset($item['properties']['in-reply-to'])) $type = 'in-reply-to';
			}
		}

		return $type;
	}

?>