<?php

	function gravatar_url($email, $s=80, $d='mm', $r='g', $img=false)
	{
		$url = '//www.gravatar.com/avatar/';
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



	function hcard_photo_fallback($author_photo)
	{
		if (empty($author_photo)) return SITE_BASE_URL.'img/no-hcard.png';
		return $author_photo;
	}

	function hcard_url_fallback($author_url, $source)
	{
		if (empty($author_url)) return $source;
		return $author_url;
	}

	function hcard_author_name_fallback($author_name)
	{
		if (empty($author_name)) return 'Someone';
		return $author_name;
	}

	function hcard_author_fallback($author_name, $author_url, $source)
	{
		if (empty($author_name)) return 'Someone';
		$author_url = hcard_url_fallback($author_url, $source);
		return "<a href=\"$author_url\">$author_name</a>";
	}

	function comment_permalink_fallback($published, $source)
	{
		if (empty($published) or '0000-00-00 00:00:00' == $published) return "<a href=\"$source\">here</a>";
		return "on <a href=\"$source\">".date('j M Y', strtotime($published)).'</a>';
	}

	function mention_type_past_tense_linked($type, $target, $id)
	{
		$mentions = array (
			'in-reply-to' => "<a href=\"$target#mention_$id\">commented</a> on",
			'like' => "<a href=\"$target#mention_$id\">liked</a>",
			'repost' => "<a href=\"$target#mention_$id\">reposted</a>",
			'mention' => "<a href=\"$target#mention_$id\">mentioned</a>"
		);

		return isset($mentions[$type]) ? $mentions[$type] : 'mentioned';
	}

?>
