# Kevin
# Structure for table "zt_kevincalendar" 
#

CREATE TABLE IF NOT EXISTS `zt_kevincalendar` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `calendar` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` enum('nor','hol','law') NOT NULL DEFAULT 'nor',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `desc` text NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
