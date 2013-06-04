<?php

	use phpish\http;


	function send_webmention($source, $target)
	{
		$response_headers = $matches = array();
		$target_webmention_endpoint = false;
		$response_body = http\request("GET $target", array(), array(), $response_headers);
		if (isset($response_headers['link']) and preg_match('#<(httpds?://[^>]+)>; rel="http://webmention.org/"#', $response_headers['link'], $matches))
		{
			$target_webmention_endpoint = $matches[1];
		}
		elseif (preg_match('#<link href="([^"]+)" rel="http://webmention.org/" ?/?>#i', $response_body, $matches))
		{
			$target_webmention_endpoint = $matches[1];
		}

		if ($target_webmention_endpoint)
		{
			$response_body = http\request("POST $target_webmention_endpoint", array(), array('source'=>$source, 'target'=>$target), $response_headers);
			print_r($response_headers);
			print_r($response_body);
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

?>