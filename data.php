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

	function db_one_row_affected()
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

	//TODO: Do an upsert here; add only if it doesn't exist.
	function db_add_post_channel($post_id, $channel_name, $now, $is_private)
	{
		return mysql\query("INSERT INTO channels (name, post_id, created_at, private) VALUES ('%s', %d, '%s', %d)", array($channel_name, $post_id, $now, $is_private));
	}


	function db_add_webmention($post_id, $source, $source_hash, $target, $target_hash, $now, $type, $content, $author_name, $author_url, $author_photo, $published)
	{
		return mysql\query("INSERT INTO webmentions (post_id, source, source_hash, target, target_hash, created_at, updated_at, type, content, author_name, author_url, author_photo, published) VALUES ('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE updated_at = '%s', content = '%s', author_name = '%s', author_url = '%s', author_photo = '%s', published = '%s'", array($post_id, $source, $source_hash, $target, $target_hash, $now, $now, $type, $content, $author_name, $author_url, $author_photo, $published, $now, $content, $author_name, $author_url, $author_photo, $published));
	}

	function db_get_webmentions($post_id, $type)
	{
		return mysql\rows("SELECT * FROM webmentions where post_id = %d and type = '%s' ORDER BY created_at", array($post_id, $type));
	}

	function db_get_all_webmentions()
	{
		return mysql\rows("SELECT id, post_id, type, source, target, author_name FROM webmentions ORDER BY updated_at DESC LIMIT 20");
	}


	function db_get_webmention_type_counts($post_id)
	{
		return mysql\rows('SELECT type, count(type) as count FROM webmentions where post_id = %d GROUP BY type', array($post_id));
	}


	function db_error()
	{
		return mysql\error();
	}
?>