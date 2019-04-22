# Author: Kevin Yang
# Date: 2016-10-14 

#
# Structure for table "kevindevice_group"
#

CREATE TABLE IF NOT EXISTS `kevindevice_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL,
  `type` enum('station','laptop','server','other','discard') NOT NULL DEFAULT 'station',
  `desc` char(255) NOT NULL DEFAULT '',
  `createdate` date NOT NULL DEFAULT '0000-00-00',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
#
# Structure for table "kevindevice_devlist"
#

CREATE TABLE IF NOT EXISTS `kevindevice_devlist` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `label` varchar(100) DEFAULT NULL,
  `group` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` enum('station','thinclient','pc','vxstation') NOT NULL DEFAULT 'station',
  `status` enum('normal','discard','wrong','unknown') NOT NULL DEFAULT 'normal',
  `dept` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `charge` char(30) NOT NULL DEFAULT '',
  `user` char(30) NOT NULL DEFAULT '',
  `version` char(50) NOT NULL DEFAULT '',
  `join` date NOT NULL DEFAULT '0000-00-00',
  `dieDate` date NOT NULL DEFAULT '0000-00-00',
  `desc` text NOT NULL,
  `deleted` tinyint(3) NOT NULL DEFAULT '0',
  `tcpip` char(50) NOT NULL DEFAULT '',
  `manageip` char(50) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '1',
  `cpuID` char(50) NOT NULL DEFAULT '',
  `deviceSN` char(50) NOT NULL DEFAULT '',
  `monitorSN` varchar(50) NOT NULL DEFAULT '',
  `monitorVersion` varchar(50) NOT NULL DEFAULT '',
  `assetNumber` varchar(50) NOT NULL DEFAULT '',
  `vidioCard` varchar(50) NOT NULL DEFAULT '',
  `discCapacity` varchar(50) NOT NULL DEFAULT '',
  `memoryCapacity` varchar(50) NOT NULL DEFAULT '',
  `system` varchar(50) NOT NULL DEFAULT '',
  `mac` varchar(50) NOT NULL DEFAULT '',
  `purpose` varchar(100) NOT NULL DEFAULT '',
  `loginaddr` varchar(255) NOT NULL DEFAULT '',
  `displayName` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `dept` (`dept`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


# this is update sql if you do not have these columns. 2016-7-28
#ALTER TABLE `kevindevice_devlist` ADD `manageip` char(50) NOT NULL DEFAULT '';
#ALTER TABLE `kevindevice_devlist` ADD `count` int(11) NOT NULL DEFAULT '1';
#ALTER TABLE `kevindevice_devlist` ADD `loginaddr` varchar(255) NOT NULL DEFAULT '';

# this is update sql if you do not have these columns. 2016-10-14
#ALTER TABLE `kevindevice_group` CHANGE `type` enum('station','laptop','server','other','discard') NOT NULL DEFAULT 'station';