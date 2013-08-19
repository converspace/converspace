<?php

	namespace link_header;

	/*

	Tested against the following examples from RFC 5988 (http://tools.ietf.org/html/rfc5988#section-5.5)

	$link_values = array(
		'<http://example.com/TheBook/chapter2>; rel="previous"; title="previous, chapter"',
		'</>; rel="http://example.net/foo"',
		'</TheBook/chapter2>; rel="previous"; title*=UTF-8\'de\'letztes%20Kapitel, </TheBook/chapter4>; rel="next"; title*=UTF-8\'de\'n%c3%a4chstes%20Kapitel',
		'<http://example.org/>; rel="start http://example.net/relation/other"'
	);

	print_r(parse($link_values));

	*/


	function parse($link_values) {
		if (is_string($link_values)) $link_values = array($link_values);

		$links = array();

		foreach ($link_values as $link_value) {

			$state = 'link_start';
			$link = array();
			$uri = $param_name = $param_value = '';

			$link_value = trim($link_value);

			$len = strlen($link_value);

			foreach (str_split($link_value) as $chr) {
				switch ($state) {
					case 'link_start':
						if ('<' == $chr) {
							$state = 'uri_start';
							$uri = '';
							$link = array();
						}
						break;
					case 'uri_start':
						if ('>' == $chr) {
							$state = 'uri_end';
							$link['uri'] = $uri;

						}
						else $uri .= $chr;
						break;
					case 'uri_end':
						if (';' == $chr) {
							$state = 'param_start';
						}
						break;
					case 'param_start':
						if (!is_whitespace($chr))
						{
							$state = 'param_name_start';
							$param_name = $chr;
						}
						else continue;
						break;
					case 'param_name_start':
						if ('=' == $chr) {
							$state = 'param_name_end';
						}
						else $param_name .= $chr;
						break;
					case 'param_name_end':
						$param_value = '';
						if ('"' == $chr) {
							$state = 'quoted_param_value_start';
						}
						else $state = 'param_value_start';
						break;
					case 'quoted_param_value_start':
						if ('"' == $chr) $state = 'quoted_param_value_end';
						else $param_value .= $chr;
						break;
					case 'quoted_param_value_end':
						if (';' == $chr) $state = 'param_value_end';
						elseif (',' == $chr) $state = 'end_of_params';
						break;
					case 'param_value_start':
						if (';' == $chr) $state = 'param_value_end';
						elseif (',' == $chr) $state = 'end_of_params';
						else $param_value .= $chr;
						break;
					case 'param_value_end':
						$state = 'param_start';
						$link[$param_name] = $param_value;
						break;
					case 'end_of_params':
						$state = 'link_start';
						$link[$param_name] = $param_value;
						if (isset($link['rel'])) foreach (explode(' ', $link['rel']) as $rel) $links[$rel] = $link;
						else $links[] = $link;
				}
			}

			if ('link_start' != $state) {
				$link[$param_name] = $param_value;
				if (isset($link['rel'])) foreach (explode(' ', $link['rel']) as $rel) $links[$rel] = $link;
				else $links[] = $link;

			}
		}

		return $links;
	}

		function is_whitespace($chr) {
			return in_array($chr, array(" ", "\t", "\n", "\r", "\0", "\x0B"));
		}


?>