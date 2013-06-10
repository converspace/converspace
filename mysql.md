
### Create database and user:
```bash
$ mysql -u root -p
mysql> CREATE DATABASE converspace;
mysql> GRANT ALL PRIVILEGES ON converspace.* TO 'converspace'@'localhost' IDENTIFIED BY 'password';
mysql> FLUSH PRIVILEGES;
```

### Create Tables:
```sql

CREATE TABLE `posts` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`content` longtext NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`draft` tinyint(1) DEFAULT '0',
	`private` tinyint(1) DEFAULT '1',

	PRIMARY KEY (`id`),
	KEY `created` (`created_at`),
	KEY `updated` (`updated_at`),
	KEY `draft` (`draft`),
	KEY `private` (`private`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `channels` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`post_id` int(11) unsigned NOT NULL,
	`created_at` datetime NOT NULL,
	`private` tinyint(1) DEFAULT '1',

	PRIMARY KEY (`id`),
	KEY `channel` (`name`),
	KEY `post` (`post_id`),
	KEY `private` (`private`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `webmentions` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`post_id` int(11) unsigned NOT NULL,
	`source` mediumtext,
	`source_hash` varchar(255),
	`target` mediumtext,
	`target_hash` varchar(255),
	`created_at` datetime,
	`updated_at` datetime,
	`type` varchar(255),
	`content` longtext,
	`author_name` varchar(255),
	`author_url` mediumtext,
	`author_photo` mediumtext,

	PRIMARY KEY (`id`),
	KEY `post` (`post_id`),
	UNIQUE KEY `post_source` (`post_id`,`source_hash`),
	KEY `type` (`type`),
	KEY `created` (`created_at`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

```

References:
* http://static.pinboard.in/schema.htm
* https://github.com/freetag/freetag/blob/master/freetag.sql
* http://www.pui.ch/phred/archives/2005/04/tags-database-schemas.html
* http://www.slideshare.net/edbond/tagging-and-folksonomy-schema-design-for-scalability-and-performance
* http://core.svn.wordpress.org/trunk/wp-admin/includes/schema.php
* http://codex.wordpress.org/Database_Description
* http://posulliv.github.com/drupal/2012/08/02/drupal-er-diagram/
* http://upsitesweb.com/sites/upsites.co/files/drupal7_model_0.png
* https://github.com/drupal/drupal/blob/7.x/modules/node/node.install
* https://github.com/drupal/drupal/blob/7.x/modules/user/user.install