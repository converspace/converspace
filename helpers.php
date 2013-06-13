<?php

	use phpish\http;


	function send_webmention($source, $target)
	{
		if ($target_webmention_endpoint = discover_webmention_endpoint($target))
		{
			$response_body = http\request("POST $target_webmention_endpoint", array(), array('source'=>$source, 'target'=>$target), $response_headers);
			print_r(compact('source', 'target', 'target_webmention_endpoint', 'response_headers', 'response_body'));
		}


	}

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

	function gravatar_url($email, $s=80, $d='mm', $r='g', $img=false)
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";

		return $url;
	}


	function dopplr_color($str)
	{
		return substr(md5($str), 0, 6);
	}

	function session_alert($type=NULL, $msg=NULL)
	{
		$alert = array();

		if (is_null($type) and is_null($msg))
		{
			if (isset($_SESSION['alert']) and !empty($_SESSION['alert']))
			{
				$alert = array('type'=>$_SESSION['alert']['type'], 'msg'=>$_SESSION['alert']['msg'], 'sess'=>$_SESSION);
				unset($_SESSION['alert']);
			}
		}
		else
		{
			$alert = array('type'=>$type, 'msg'=>$msg);
			$_SESSION['alert'] = $alert;
		}

		return $alert;
	}


	// http://sandeep.shetty.in/2013/06/extracting-machine-triple-tags-from-a-string.html
	function extract_machinetags($str)
	{
		preg_match_all('/#(?P<namespace>[a-zA-Z_][a-zA-Z0-9_\-]+):(?P<predicate>[a-zA-Z_][a-zA-Z0-9_\-]+)=(["\'])?(?P<value>(?(3)(?:(?:(?!\3|\\\\).|\\\\\\3)*)|(?:[^\s]+)))(?(3)\\3)/s', $str, $matches);

		array_walk($matches['value'], function (&$value, $key) {
			$value = stripslashes($value);
		});

		$machinetags = array();
		$machinetags[0] = $matches[0];
		foreach ($matches['namespace'] as $key=>$val)
		{
			$machinetags[$val][$matches['predicate'][$key]] = $matches['value'][$key];
		}

		return $machinetags;
	}

	function strip_machinetags($str, $machinetags)
	{
		foreach ($machinetags as $machinetag)
		{
			$str = str_replace($machinetag, '', $str);
		}

		return trim($str);
	}


	function json_pretty_print($json) {

		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++)
		{
			$char = substr($json, $i, 1);

			if ($char == '"' && $prevChar != '\\')
			{
				$outOfQuotes = !$outOfQuotes;
			}
			else if(($char == '}' || $char == ']') && $outOfQuotes)
			{
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++)
				{
					$result .= $indentStr;
				}
			}

			$result .= $char;

			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes)
			{
				$result .= $newLine;
				if ($char == '{' || $char == '[')
				{
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

?>