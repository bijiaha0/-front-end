<?php
$config->kevinhours = new stdclass();

$config->kevinhours->batchcreate = 8;
$config->kevinhours->defaultPartment = '开发部';
//$config->kevinhours->KEVIN_USER_ACCOUNT = 'kevin_user_account';
//#define KEVIN_USER_ACCOUNT  'kevin_user_account'
//$config->kevinhours->KEVIN_USER_REALNAME = 'kevin_user_realname';

$config->kevinhours->times = new stdclass();
$config->kevinhours->times->begin           = 0;
$config->kevinhours->times->end             = 23;
$config->kevinhours->times->delta           = 10;

$config->kevinhours->times->todoWorkStart       = '0800';
$config->kevinhours->times->todoWorkEnd         = '1630';
$config->kevinhours->times->todoEatingStart  = '1130';
$config->kevinhours->times->todoEatingEnd    = '1200';

$config->kevinhours->ListSimpleModel 		  = true;

//侧边栏像素宽度
$config->kevinhours->sideWidth 		    = 200;

//添加代码
$config->kevinhours->fontColor['nor']   = '';
$config->kevinhours->fontColor['hol']   = '#00ffff';
$config->kevinhours->fontColor['ann']   = '#00ffff';
$config->kevinhours->fontColor['ove']   = 'yellow';
$config->kevinhours->fontColor['rep']   = '#00ffff';

$config->kevinhours->projectIDMax		  = 1000;//关键词搜索的项目代号的最大id
$config->kevinhours->showProjectMax	  = 5;//默认最多显示5条
$config->kevinhours->recentDays	  	  = 60;//获得最近的天数
$config->kevinhours->isShowDeletedAccount = false;//是否显示已删除的用户
$config->kevinhours->startProject 	  = 110;//起始项目代号，包含
$config->kevinhours->endProject 		  = 999;//结束项目代号
$config->kevinhours->projectForHoliday  = 2;//假期对应的项目代号

//默认的考勤锁定时间设定，可以在my.php中赋值
$config->kevinhours->limitDate		= 0;//考勤修改截止天数，0表示不起作用
$config->kevinhours->endDay			= 1;//每月考勤锁定天数，按照endDaytype进行解释，0表示不起作用
$config->kevinhours->endDayType		= 0;//0表示第几天，1表示第几个工作日，从KevinCalendar查询
$config->kevinhours->endTime		= '24:00';//小时分钟
$config->kevinhours->MonthLockEarly	= '10';//提前锁定月份

//设置备注框样式
$config->kevinhours->editor = new stdclass();
$config->kevinhours->editor->edit = array('id' => 'desc', 'tools' => 'simple');
$config->kevinhours->editor->create = array('id' => 'desc', 'tools' => 'simple');

//todo required
$config->kevinhours->create = new stdclass();
$config->kevinhours->edit = new stdclass();
$config->kevinhours->create->requiredFields = 'name,minutes,project';
$config->kevinhours->edit->requiredFields   = 'name,minutes,project';
/* Include the custom config file. my.php*/
$configRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$myConfig   = $configRoot . 'my.php';
if(file_exists($myConfig)) include $myConfig;