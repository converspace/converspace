<?php

	use phpish\mysql;


	function get_channels()
	{
		return mysql\rows('SELECT name, count(*) as count FROM channels WHERE private = 0 GROUP BY name ORDER BY count DESC');
	}

	function get_posts()
	{
		return mysql\rows('SELECT * FROM posts WHERE private = 0 ORDER BY id DESC LIMIT 11');
	}

	function get_posts_before($before)
	{
		return mysql\rows('SELECT * FROM posts WHERE private = 0 AND id < %d ORDER BY id DESC LIMIT 11', array($before));
	}

	function get_posts_after($after)
	{
		return mysql\rows('SELECT * FROM posts WHERE private = 0 AND id > %d ORDER BY id ASC LIMIT 11', array($after));
	}

	function get_channel_posts($channel_name)
	{
		return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.private = 0 ORDER BY p.id DESC LIMIT 11", array($channel_name));
	}

	function get_channel_posts_before($channel_name, $before)
	{
		return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id < %d AND p.private = 0 ORDER BY id DESC LIMIT 11", array($channel_name, $before));
	}

	function get_channel_posts_after($channel_name, $after)
	{
		return mysql\rows("SELECT p.id, p.content, p.created_at FROM posts p, channels c WHERE c.post_id = p.id AND c.name = '%s' AND p.id > %d AND p.private = 0 ORDER BY id ASC LIMIT 11", array($channel_name, $after));
	}


	function get_post($post_id)
	{
		return mysql\rows("select * from posts where id = %d", array($post_id));
	}

	function get_older_post_id($post_id)
	{
		return mysql\row("select max(id) as id from posts where id < %d", array($post_id));
	}

	function get_newer_post_id($post_id)
	{
		return mysql\row("select min(id) as id from posts where id > %d", array($post_id));
	}

	function get_post_channels($post_id)
	{
		return mysql\rows('select name from channels where post_id = %d', array($post_id));
	}

?>