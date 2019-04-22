<?php

//main menu
$lang->menu->kevinhours	 = '工时|kevinhours|index';
$lang->menuOrder[7]		 = 'kevinhours';

//kevin menu
$lang->kevin->menu->kevinhours	 = '工时|kevinhours|index';
$lang->kevin->menuOrder[10]		 = 'kevinhours';

//self menu
//$lang->menugroup->kevinhours = 'kevincom';
$lang->kevinhours			 = new stdclass();
$lang->kevinhours->menu		 = new stdclass();

//等待翻译
$lang->kevinhours->menu->index	 = '日历|kevinhours|index';
$lang->kevinhours->menu->todo	 = '列表|kevinhours|todo';
$lang->kevinhours->menu->project = '项目|kevinhours|project';
$lang->kevinhours->menu->product = '产品|kevinhours|product';
$lang->kevinhours->menu->count	 = '统计|kevinhours|count';
$lang->kevinhours->menu->service = '工时单|kevinhours|service';
$lang->kevinhours->menu->browse	 = '用户|kevinhours|browse';
$lang->kevinhours->menu->clock	 = '打卡|kevinhours|clock';
$lang->kevinhours->menu->over	 = array('link' => '加班统计|kevinhours|over', '', 'alias' => 'deptover');

$lang->kevinhours->menuOrder[5]	 = 'index';
$lang->kevinhours->menuOrder[10] = 'todo';
$lang->kevinhours->menuOrder[15] = 'my';
$lang->kevinhours->menuOrder[20] = 'project';
$lang->kevinhours->menuOrder[25] = 'product';
$lang->kevinhours->menuOrder[30] = 'count';
$lang->kevinhours->menuOrder[35] = 'service';
$lang->kevinhours->menuOrder[40] = 'browse';
$lang->kevinhours->menuOrder[45] = 'clock';
$lang->kevinhours->menuOrder[50] = 'over';
$lang->kevinhours->menuOrder[55] = 'deptover';

$lang->kevinhours->menu->all = array('link' => '本人待办|kevinhours|index|type=&account=0', 'float' => 'right');
