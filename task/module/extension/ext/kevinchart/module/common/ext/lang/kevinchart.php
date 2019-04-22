<?php

//kevin menu
$lang->kevin->menu->kevinchart	 = array('link' => 'Echarts|kevinchart|index', '', 'alias' => 'index,itemlist,mychart');
$lang->kevin->menuOrder[70]		 = 'kevinchart';

//self menu
$lang->menugroup->kevinchart = 'kevincom';
$lang->kevinchart			 = new stdclass();
$lang->kevinchart->menu		 = new stdclass();

//menu list
$lang->kevinchart->menu->index = '百度示例|kevinchart|index';
$lang->kevinchart->menu->itemlist = '我的报表|kevinchart|itemlist';
$lang->kevinchart->menu->mychart = '<i class="icon-common-report icon-bar-chart"></i>使用曲线|kevinchart|mychart';
$lang->kevinchart->menuOrder[10]	 = 'index';
$lang->kevinchart->menuOrder[20]	 = 'itemlist';
$lang->kevinchart->menuOrder[30]	 = 'mychart';
