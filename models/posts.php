<?php

	use phpish\template;

	define('TAG_REGEX', '/(^|\s|\()(#([a-zA-Z0-9_][a-zA-Z0-9\-_]*))/ms');
	define('TWITTER_USER_REGEX', '/(^|\s|\()(@([a-zA-Z0-9_]+))/ms');
	define('TRAILING_TAGS_REGEX', '/\n\n  ([^\n]*)$/s');


	function extract_tags_from_post($content)
	{
		preg_match_all(TAG_REGEX, $content, $matches);
		return $matches[3];
	}


	function extract_title_and_body_from_post($md_content)
	{
		$title = $body = '';
		if (substr($md_content, 0, 2) == '# ') list($title, $body) = preg_split('/\n/', $md_content, 2);
		else $body = $md_content;
		return compact('title', 'body');
	}


	function add_post($post_content, $now, $is_private, $post_channels)
	{
		db_add_post($post_content, $now, $is_private);
		if (db_one_row_affected())
		{
			$post_id = db_insert_id();

			foreach($post_channels as $channel_name)
			{
				db_add_post_channel($post_id, $channel_name, $now, $is_private);
			}

			session_alert('success', 'Post Saved!');
			return $post_id;
		}
		else
		{
			error_log('Error while saving post: '.mysql\error());
			session_alert('error', 'Sorry! Error while saving post!');
		}
	}


	function update_post($post_id, $post_content, $now, $is_private, $post_channels)
	{
		db_update_post($post_id, $post_content, $now, $is_private);
		if (db_one_row_affected())
		{
			$channels_to_delete = array();
			$existing_channels_rows = db_get_post_channels($post_id);
			foreach ($existing_channels_rows as $existing_channel_row)
			{
				if (false === ($key = array_search($existing_channel_row['name'], $post_channels)))
				{
					$channels_to_delete[] = $existing_channel_row['name'];
				}
				else unset($post_channels[$key]);
			}

			if (!empty($channels_to_delete)) delete_post_channels($post_id, $channels_to_delete);

			foreach($post_channels as $channel_name)
			{
				db_add_post_channel($post_id, $channel_name, $now, $is_private);
			}
		}
	}


	function get_post($post_id, $authorized)
	{
		$pager = array();
		$md_posts = db_get_post($post_id, $authorized);
		$posts = prepare_posts($md_posts);

		$older_post = db_get_older_post_id($post_id, $authorized);
		if (isset($older_post['id'])) $pager['before'] = $older_post['id'];
		$newer_post = db_get_newer_post_id($post_id, $authorized);
		if (isset($newer_post['id'])) $pager['after'] = $newer_post['id'];

		return array($posts, $pager);
	}

	function get_posts($req, $channel_name, $authorized)
	{
		$pager = array();
		$more_posts = array();
		list($direction, $from_post_id) = direction_and_from_post_id($req['query']);
		$md_posts = empty($channel_name) ? db_get_posts($direction, $from_post_id, $authorized) : db_get_channel_posts($channel_name, $direction, $from_post_id, $authorized);
		if (count($md_posts) === 11) $more_posts = array_pop($md_posts);
		if ($more_posts) $pager[$direction] = $md_posts[9]['id'];
		if (!is_homepage($from_post_id)) $pager[opp_direction($direction)] = $md_posts[0]['id'];
		if ('after' == $direction) $md_posts = array_reverse($md_posts);
		$posts = prepare_posts($md_posts);
		return array($posts, $pager);
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
				$post = extract_title_and_body_from_post($md_post['content']);
				$post['body'] = strip_empty_lines($post['body']);
				$post['body'] = normalize_line_ending($post['body']);


				$machinetags = extract_machinetags($post['body']);
				$post['body'] = strip_machinetags($post['body'], $machinetags[0]);
				if (isset($machinetags['repost']))
				{
					$repost = template\render('repost.html', $machinetags);
					$post['body'] = "<div>Reposted a <a href=\"{$machinetags['repost']['url']}\">post</a> by <a href=\"{$machinetags['repost']['author_url']}\">{$machinetags['repost']['author']}</a>.</div>".$post['body'].$repost;
				}

				$post['body'] = trailing_tags_filter($post['body']);
				$post['body'] = tag_syntax_filter($post['body']);
				$post['body'] = twitter_user_syntax_filter($post['body']);
				$content = Markdown(implode("\n", $post));
				$posts[] = array('title'=>$post['title'], 'body'=>$post['body'], 'raw'=>$md_post['content'], 'content'=>$content, 'id'=>$md_post['id'], 'created_at'=>$md_post['created_at']);
			}

			return $posts;
		}

			function normalize_line_ending($content)
			{
				return preg_replace('/\r\n?/', "\n", $content);
			}

			function strip_empty_lines($content)
			{
				return preg_replace('/^[ \t]+$/m', '', $content);
			}

			function tag_syntax_filter($content)
			{
				return preg_replace(TAG_REGEX, '$1<span class="deem">#</span><a class="p-category" href="'.SITE_BASE_URL.'$3/" rel="tag">$3</a>', $content);
			}

			function twitter_user_syntax_filter($content)
			{
				return preg_replace(TWITTER_USER_REGEX, '$1<span class="deem">@</span><a href="https://twitter.com/$3">$3</a>', $content);
			}

			function trailing_tags_filter($content)
			{
				return preg_replace(TRAILING_TAGS_REGEX, "\n\n  <div class=\"additional_tags\">\n$1\n</div>", $content);
			}

?>