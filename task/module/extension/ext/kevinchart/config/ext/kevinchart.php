<?php

$config->kevinchart_prefix = $config->db->prefix . 'kevin_';
//db appdaily
define('TABLE_KEVIN_CHARTEXAMPLE', '`' . $config->kevinchart_prefix . 'chartexample`');
$config->objectTables['kevin_chartexample']       = TABLE_KEVIN_CHARTEXAMPLE;
