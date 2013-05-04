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

?>