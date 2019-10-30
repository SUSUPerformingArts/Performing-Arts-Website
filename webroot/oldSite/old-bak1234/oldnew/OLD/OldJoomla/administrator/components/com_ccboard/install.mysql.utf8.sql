CREATE TABLE IF NOT EXISTS `#__ccb_attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL default '0',
  `ccb_name` varchar(255) NOT NULL default ' ',
  `real_name` varchar(255) NOT NULL default ' ',
  `filesize` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `comment` mediumtext NOT NULL,
  `filetime` int(10) unsigned NOT NULL default '0',
  `extension` varchar(100) default NULL,
  `mimetype` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cat_name` varchar(255) default '.',
  `ordering` int(10) unsigned default '0',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `forum_name` varchar(255) default '.',
  `forum_desc` mediumtext,
  `cat_id` int(10) unsigned default '0',
  `topic_count` int(10) unsigned default '0',
  `post_count` int(10) unsigned default '0',
  `last_post_user` int(10) unsigned default '0',
  `last_post_time` int(10) unsigned default '0',
  `last_post_id` int(10) unsigned default '0',
  `published` tinyint(3) unsigned default '0',
  `locked` tinyint(3) unsigned default '0',
  `view_for` int(10) unsigned default '0',
  `post_for` int(10) unsigned default '18',
  `moderate_for` int(10) unsigned default '23',
  `forum_image` varchar(100) default '',
  `ordering` int(10) unsigned default '0',
  `moderated` tinyint(3) unsigned NOT NULL default '0',
  `review` tinyint(3) unsigned NOT NULL default '0',
  `last_post_username` varchar(255) default '',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_moderators` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `forum_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(10) unsigned default '0',
  `forum_id` int(10) unsigned default '0',
  `post_subject` varchar(255) default '.',
  `post_text` mediumtext NOT NULL,
  `post_user` int(10) unsigned default '0',
  `post_time` int(10) unsigned default '0',
  `ip` varchar(20) default NULL,
  `hold` tinyint(3) unsigned default '0',
  `modified_by` int(10) unsigned default '0',
  `modified_time` int(10) unsigned default '0',
  `modified_reason` varchar(255) default '',
  `post_username` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ccb_posts_topic_id` (`topic_id`) 
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_ranks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `rank_title` varchar(255) NOT NULL,
  `rank_min` int(10) unsigned NOT NULL default '0',
  `rank_special` tinyint(3) unsigned NOT NULL default '0',
  `rank_image` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned default '0',
  `post_subject` varchar(255) default '.',
  `reply_count` int(10) unsigned default '0',
  `hits` int(10) unsigned default '0',
  `post_time` int(10) unsigned default '0',
  `post_user` int(10) unsigned default '0',
  `last_post_time` int(10) unsigned default '0',
  `last_post_id` int(10) unsigned default '0',
  `last_post_user` int(10) unsigned default '0',
  `start_post_id` int(10) unsigned default '0',
  `topic_type` tinyint(3) unsigned default '0',
  `locked` tinyint(3) unsigned default '0',
  `topic_email` mediumtext,
  `hold` tinyint(3) unsigned default '0',
  `topic_emoticon` tinyint(3) unsigned default '0',
  `post_username` varchar(255) default NULL,
  `last_post_username` varchar(255) default '',
  `topic_favourite` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `ccb_topics_forum_id` (`forum_id`),
  KEY `ccb_topics_last_post_time` (`last_post_time`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ccb_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `dob` int(11) default '0',
  `location` varchar(45) default NULL,
  `signature` mediumtext,
  `avatar` varchar(100) default NULL,
  `rank` int(10) unsigned NOT NULL default '0',
  `post_count` int(10) unsigned default '0',
  `gender` char(10) default 'Male',
  `www` varchar(45) default NULL,
  `icq` varchar(45) default NULL,
  `aol` varchar(45) default NULL,
  `msn` varchar(45) default NULL,
  `yahoo` varchar(45) default NULL,
  `jabber` varchar(45) default NULL,
  `skype` varchar(45) default NULL,
  `thumb` varchar(100) default NULL,
  `showemail` tinyint(3) unsigned default '0',
  `moderator` tinyint(3) unsigned default '0',
  `karma` int(10) signed default '0',
  `karma_time` int(10) unsigned default '0',
  `hits` int(10) unsigned default '0',
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;


