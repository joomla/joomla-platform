# @package     Joomla.Platform
# @subpackage  Content
# @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
# @license     GNU General Public License version 2 or later; see LICENSE

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `jos_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `temporary` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(10) unsigned DEFAULT NULL,
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned DEFAULT NULL,
  `checked_out_session` varchar(255) NOT NULL DEFAULT '',
  `checked_out_user_id` int(10) unsigned DEFAULT NULL,
  `publish_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `likes` int(10) unsigned NOT NULL DEFAULT '0',
  `revision` int(10) unsigned NOT NULL DEFAULT '0',
  `config` mediumtext NOT NULL,
  `media` text NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `type_id` (`type_id`),
  KEY `idx_visibility` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`),
  KEY `idx_visibility_created` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`created_date`),
  KEY `idx_visibility_modified` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`modified_date`),
  KEY `idx_visibility_likes` (`type_id`,`state`,`access`,`publish_start_date`,`publish_end_date`,`likes`),
  KEY `modified_user_id` (`modified_user_id`),
  KEY `checked_out_user_id` (`checked_out_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `jos_content_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `jos_content_types` (`type_id`),
  CONSTRAINT `jos_content_ibfk_2` FOREIGN KEY (`modified_user_id`) REFERENCES `jos_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jos_content_ibfk_3` FOREIGN KEY (`checked_out_user_id`) REFERENCES `jos_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `jos_content_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `jos_users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_content_hits` (
  `content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `hit_modified_date` datetime DEFAULT NULL COMMENT 'The time that the content was last hit.',
  PRIMARY KEY (`content_id`),
  KEY `idx_hits` (`hits`),
  CONSTRAINT `jos_content_hits_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `jos_content` (`content_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_content_likes` (
  `content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `like_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '+1 if the user likes the content, -1 if the user explicitly dislikes the content.',
  `like_modified_date` datetime DEFAULT NULL COMMENT 'The time that the like was updated',
  PRIMARY KEY (`content_id`,`user_id`),
  KEY `member_id` (`user_id`),
  CONSTRAINT `jos_content_likes_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `jos_content` (`content_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_content_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
