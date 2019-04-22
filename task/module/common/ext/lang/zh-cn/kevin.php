<?php
include (dirname(__FILE__) . '/../kevin.php');
$lang->menu->kevincom	= '凯文|kevincom|index';

$lang->kevincom->menu->index  = '首页|kevincom|index';

$myConfig   = (dirname(__FILE__) . '/kevinmy.php');
if(file_exists($myConfig)) include $myConfig;