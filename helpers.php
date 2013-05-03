<?php

	function tag_syntax_filter($content)
	{
		$tag_regex = '/(^|\s|\()(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms';
		return preg_replace($tag_regex, '$1<span class="deem">#</span><a href="'.SITE_BASE_URL.'channels/$3" rel="tag">$3</a>', $content);
	}


	function twitter_user_syntax_filter($content)
	{
		$twitter_user_regex = '/(^|\s|\()(@([a-zA-Z0-9_]+))/ms';
		return preg_replace($twitter_user_regex, '$1<span class="deem">@</span><a href="https://twitter.com/$3">$3</a>', $content);
	}


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

	function prepare_posts($md_posts)
	{
		$posts = array();
		foreach ($md_posts as $md_post)
		{
			$content = $title = '';
			if (substr($md_post['content'], 0, 2) == '# ') list($title, $content) = preg_split('/\n/', $md_post['content'], 2);
			else $content = $md_post['content'];

			$content = tag_syntax_filter($content);
			$content = twitter_user_syntax_filter($content);

			if (!empty($title)) $content = "$title\n$content";
			$content = Markdown($content);
			$posts[] = array('title'=>$title, 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at'], 'title'=>$title);
		}

		return $posts;
	}



?>