# Date: 2015-7-8
# update to custom for zentao 

#
# Structure for table "zt_hoursproject"
#
CREATE TABLE IF NOT EXISTS `zt_hoursproject` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `dept` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `deptdispatch` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `project` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `account` char(30) NOT NULL DEFAULT '',
  `year` int(4) NOT NULL DEFAULT '0',
  `month` int(2) NOT NULL DEFAULT '0',
  `cashCode` varchar(45) NOT NULL DEFAULT '',
  `hours` float(11,3) NOT NULL DEFAULT '0.000',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `lastEditedBy` varchar(30) NOT NULL,
  `lastEditedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`project`,`account`,`year`,`month`)
) ENGINE=MyISAM AUTO_INCREMENT=1610 DEFAULT CHARSET=utf8;
#ALTER TABLE `zt_hoursproject` ADD `dept` mediumint(8) unsigned NOT NULL DEFAULT '0';
#ALTER TABLE `zt_hoursproject` ADD `deptdispatch` mediumint(8) unsigned NOT NULL DEFAULT '0';

#
# Structure for table "zt_hourscashcode"
#
CREATE TABLE IF NOT EXISTS `zt_hourscashcode` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `dept` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `deptdispatch` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `account` char(30) NOT NULL DEFAULT '',
  `year` int(4) NOT NULL DEFAULT '0',
  `month` int(2) NOT NULL DEFAULT '0',
  `cashCode` varchar(45) NOT NULL DEFAULT '',
  `hours` float(11,3) NOT NULL DEFAULT '0.000',
  `amountto` float(11,3) NOT NULL DEFAULT '0.000',
  `total` float(11,3) NOT NULL DEFAULT '0.000',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `lastEditedBy` varchar(30) NOT NULL,
  `lastEditedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`account`,`year`,`month`,`cashCode`)
) ENGINE=MyISAM AUTO_INCREMENT=841 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
#ALTER TABLE `zt_hourscashcode` ADD `dept` mediumint(8) unsigned NOT NULL DEFAULT '0';
#ALTER TABLE `zt_hourscashcode` ADD `deptdispatch` mediumint(8) unsigned NOT NULL DEFAULT '0';

#
# Structure for table "zt_kevinclockact"
#
CREATE TABLE IF NOT EXISTS `zt_kevinclockact` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account` char(30) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `time` smallint(4) unsigned zerofill NOT NULL,
  `action` enum('in','out') NOT NULL DEFAULT 'in',
  `desc` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`account`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
#The following can do 
#update zt_kevinclockact a set a.action = 'out' where a.action != 'in'


#the following you can select to do
# for table `zt_project`
#ALTER TABLE `zt_project` ADD `cashCode` varchar(45) NOT NULL DEFAULT '';

# for table `zt_todo`
#ALTER TABLE `zt_todo` ADD `hourstype` char(10) NOT NULL DEFAULT 'nor';
#ALTER TABLE `zt_todo` ADD `project` int(11) NOT NULL DEFAULT 0;
#ALTER TABLE `zt_todo` ADD `minutes` int(11) unsigned NOT NULL DEFAULT 0;

# for table `zt_user`
#ALTER TABLE `zt_user` ADD `code` varchar(45) NOT NULL DEFAULT '';
#ALTER TABLE `zt_user` ADD `deptdispatch` mediumint(8) unsigned NOT NULL DEFAULT '0';
#ALTER TABLE `zt_user` ADD `ratepay` double NOT NULL DEFAULT '20';

#for table `zt_dept`
#ALTER TABLE `zt_dept` ADD  `deleted` enum('0','1') NOT NULL DEFAULT '0';

#update zt_user a set a.ratepay = 20 where a.ratepay = 0 ;
#去除Todo的除了view 权限
#delete a.* from zt_grouppriv a where a.module = 'todo' and a.method != 'view';

#ALTER TABLE `zt_kevinclockact` ADD `desc` text NOT NULL;
#ALTER TABLE `zt_kevinclockact` MODIFY `action` enum('in','out') NOT NULL DEFAULT 'in';


#ALTER TABLE `zt_user` ADD `domainFullAccount` char(100) DEFAULT NULL COMMENT 'such as kevin@tom.com';
  
#ALTER TABLE `zt_user` ADD UNIQUE KEY `kevin_domainFullAccount_unique` (`domainFullAccount`);
#update zt_user set domainFullAccount = null where domainFullAccount = "";