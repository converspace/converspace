
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
	`user_id` int(11) unsigned NOT NULL,
	`content` longtext NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	`draft` tinyint(1) DEFAULT '0',
	`private` tinyint(1) DEFAULT '1',

	PRIMARY KEY (`id`),
	KEY `user` (`user_id`),
	KEY `created` (`created_at`),
	KEY `updated` (`updated_at`),
	KEY `draft` (`draft`),
	KEY `private` (`private`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `channels` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`user_id` int(11) unsigned NOT NULL,
	`post_id` int(11) unsigned NOT NULL,
	`created_at` datetime NOT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `userpostchannel` (`user_id`,`post_id`,`name`),
	KEY `user` (`user_id`),
	KEY `channel` (`name`),
	KEY `post` (`post_id`),
	KEY `userchannel` (`user_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL default '',
	`email` varchar(255) NOT NULL,
	`created_at` datetime NOT NULL,

	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`email`)
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