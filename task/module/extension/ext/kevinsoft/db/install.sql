# Author: Kevin Yang
# Date: 2016-08-04 

#
# Structure for table "kv_soft_file"
#

CREATE TABLE IF NOT EXISTS `kv_soft_file` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pathname` char(50) NOT NULL,
  `title` char(90) NOT NULL,
  `extension` char(30) NOT NULL,
  `size` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `objectType` char(30) NOT NULL,
  `objectID` mediumint(9) NOT NULL,
  `addedBy` char(30) NOT NULL DEFAULT '',
  `addedDate` datetime NOT NULL,
  `downloads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `extra` varchar(255) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Structure for table "kv_soft_groupversion"
#

CREATE TABLE IF NOT EXISTS `kv_soft_groupversion` (
  `groupversion` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `version` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  UNIQUE KEY `kv_update_versionfiles_unique` (`groupversion`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Structure for table "kv_soft_list"
#

CREATE TABLE IF NOT EXISTS `kv_soft_list` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `IID` char(50) NOT NULL DEFAULT '',
  `name` char(50) NOT NULL DEFAULT '',
  `valid` enum('0','1') NOT NULL DEFAULT '1',
  `type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0:单个软件；1，软件组',
  `addedBy` char(30) NOT NULL DEFAULT '',
  `addedDate` datetime NOT NULL,
  `lastEditedBy` varchar(29) NOT NULL DEFAULT '',
  `lastEditedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kv_update_soft_IID` (`IID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Structure for table "kv_soft_module"
#

CREATE TABLE IF NOT EXISTS `kv_soft_module` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `device` mediumint(9) NOT NULL DEFAULT '0',
  `software` mediumint(9) NOT NULL DEFAULT '0',
  `type` enum('float','fix') NOT NULL DEFAULT 'fix',
  `module` varchar(50) NOT NULL DEFAULT '',
  `notes` varchar(100) NOT NULL DEFAULT '',
  `count` mediumint(9) NOT NULL DEFAULT '1',
  `startDate` date NOT NULL DEFAULT '0000-00-00',
  `endDate` date NOT NULL DEFAULT '0000-00-00',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

#
# Structure for table "kv_soft_version"
#

CREATE TABLE IF NOT EXISTS `kv_soft_version` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `soft` mediumint(9) NOT NULL DEFAULT '0',
  `version` char(50) NOT NULL DEFAULT '',
  `downloads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `valid` enum('0','1') NOT NULL DEFAULT '1',
  `md5` char(32) NOT NULL DEFAULT '',
  `replaceType` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0:全目录替换；1，部分文件替换',
  `addedBy` char(50) NOT NULL DEFAULT '',
  `addedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastEditedBy` varchar(29) NOT NULL DEFAULT '',
  `lastEditedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0:全目录替换；1，部分文件替换',
  `name` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kv_update_version_nameversion` (`soft`,`version`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
