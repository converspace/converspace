<?php

	use phpish\mysql;


	function db_get_channels($show_private=false)
	{
		if ($show_private) return mysql\rows('SELECT name, count(*) as count FROM channels GROUP BY name ORDER BY count DESC');
		else return mysql\rows('SELECT name, count(*) as count FROM channels WHERE private = 0 GROUP BY name ORDER BY count DESC');
	}

	function db_get_posts($direction, $from_post_id, $show_private=false)
	{
		if ($show_private)
		{
			if ('before' == $direction)
			{
				if (is_homepage($from_post_id)) return mysql\rows('SELECT * FROM posts ORDER BY id DESC LIMIT 11');
				else return mysql\rows('SELECT * FROM posts WHERE id < %d ORDER BY id DESC LIMIT 11', array($from_post_id));
			}
			elseif ('after' == $direction) return mysql\rows('SELECT * FROM posts WHERE id > %d ORDER BY id ASC LIMIT 11', array($from_post_id));
		}
		else
		{
			if ('before' == $direction)
			{
				if (is_homepage($from_post_id)) return mysql\rows('SELECT * FROM posts WHERE private = 0 ORDER BY id DESC LIMIT 11');
				else return mysql\rows('SELECT * FROM posts WHERE private = 0 AND id < %d ORDER BY id DESC LIMIT 11', array($from_post_id));
			}
			elseif ('after' == $direction) return mysql\rows('SELECT * FROM posts WHERE private = 0 AND id > %d ORDER BY id ASC LIMIT 11', array($from_post_id));
		}
	}

	function db_get_channel_posts($channel_name, $direction, $from_post_id, $show_private=false)
	{
		if ($show_private)
		{
			if ('before' == $direction)
			{
				if (is_homepage($from_post_id)) return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' ORDER BY p.id DESC LIMIT 11", array($channel_name));
				else return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id < %d ORDER BY id DESC LIMIT 11", array($channel_name, $from_post_id));
			}
			elseif ('after' == $direction) mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id > %d ORDER BY id ASC LIMIT 11", array($channel_name, $from_post_id));
		}
		else
		{
			if ('before' == $direction)
			{
				if (is_homepage($from_post_id)) return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.private = 0 ORDER BY p.id DESC LIMIT 11", array($channel_name));
				else return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id < %d AND p.private = 0 ORDER BY id DESC LIMIT 11", array($channel_name, $from_post_id));
			}
			elseif ('after' == $direction) mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id > %d AND p.private = 0 ORDER BY id ASC LIMIT 11", array($channel_name, $from_post_id));
		}
	}

	function db_get_post($post_id, $show_private=false)
	{
		if ($show_private) return mysql\rows("select * from posts where id = %d", array($post_id));
		else return mysql\rows("select * from posts where id = %d  AND private = 0", array($post_id));
	}

	function db_get_older_post_id($post_id, $show_private=false)
	{
		if ($show_private) return mysql\row("select max(id) as id from posts where id < %d", array($post_id));
		else return mysql\row("select max(id) as id from posts where id < %d AND private = 0", array($post_id));
	}

	function db_get_newer_post_id($post_id, $show_private=false)
	{
		if ($show_private) return mysql\row("select min(id) as id from posts where id > %d", array($post_id));
		else return mysql\row("select min(id) as id from posts where id > %d AND private = 0", array($post_id));
	}

	function db_add_post($post_content, $now, $is_private)
	{
		return mysql\query("INSERT INTO posts (content, created_at, updated_at, private) VALUES ('%s', '%s', '%s', %d)", array($post_content, $now, $now, $is_private));
	}

	function db_is_successfully_added()
	{
		return (mysql\affected_rows() === 1);
	}

	function db_insert_id()
	{
		return mysql\insert_id();
	}

	function db_update_post($post_id, $post_content, $now, $is_private)
	{
		return mysql\query("UPDATE posts SET content = '%s', updated_at = '%s', private = %d WHERE id = %d", array($post_content, $now, $is_private, $post_id));
	}

	function db_get_post_channels($post_id)
	{
		return mysql\rows('select name from channels where post_id = %d', array($post_id));
	}

	function db_delete_post_channels($post_id, $channels_to_delete)
	{
		return mysql\query("DELETE FROM channels WHERE post_id = %d and name in ('".implode("','", $channels_to_delete)."')", array($post_id));
	}

	function db_add_post_channel($post_id, $channel_name, $now, $is_private)
	{
		return mysql\query("INSERT INTO channels (name, post_id, created_at, private) VALUES ('%s', %d, '%s', %d)", array($channel_name, $post_id, $now, $is_private));
	}

?>