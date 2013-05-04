<?php

	function get_posts($req)
	{
		$channel_name = isset($req['matches']['channel']) ? $req['matches']['channel'] : '';
		$pager = array();
		$more_posts = array();
		list($direction, $from_post_id) = direction_and_from_post_id($req['query']);
		$md_posts = empty($channel_name) ? db_get_posts($direction, $from_post_id) : db_get_channel_posts($channel_name, $direction, $from_post_id);
		if (count($md_posts) === 11) $more_posts = array_pop($md_posts);
		if ($more_posts) $pager[$direction] = $md_posts[9]['id'];
		if (!is_homepage($from_post_id)) $pager[opp_direction($direction)] = $md_posts[0]['id'];
		if ('after' == $direction) $md_posts = array_reverse($md_posts);
		$posts = prepare_posts($md_posts);
		return array($posts, $pager, $channel_name);
	}

		function direction_and_from_post_id($query)
		{
			if (isset($query['before']))
			{
				$direction = 'before';
				$from_post_id = intval($query['before']);
			}
			elseif (isset($query['after']))
			{
				$direction = 'after';
				$from_post_id = intval($query['after']);
			}
			else
			{
				$direction = 'before';
				$from_post_id = NULL;
			}

			return array($direction, $from_post_id);
		}

		function opp_direction($direction)
		{
			if ('before' == $direction) return 'after';
			elseif ('after' == $direction) return 'before';
		}

		function is_homepage($from_post_id)
		{
			return is_null($from_post_id);
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

?>