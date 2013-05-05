<?php

	function gravatar($email, $s=80, $d='mm', $r='g', $img=false, $atts=array())
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img )
		{
			$url = "<img src=\"$url\"";
			foreach ($atts as $key=>$val) $url .= " $key=\"$val\"";
			$url .= ' />';
		}

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