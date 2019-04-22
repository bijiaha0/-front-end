<?php

$config->kevindevice_prefix = 'kevindevice_';
//db define
define('TABLE_KEVINDEVICE_DEVLIST', '`' . $config->kevindevice_prefix . 'devlist`');
define('TABLE_KEVINDEVICE_GROUP', '`' . $config->kevindevice_prefix . 'group`');

$config->objectTables['kevindevice_devlist']      = TABLE_KEVINDEVICE_DEVLIST;
$config->objectTables['kevindevice_group']     = TABLE_KEVINDEVICE_GROUP;
