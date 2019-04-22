<?php
//db define
$prefix = 'kv_soft_';
define('TABLE_KEVIN_SOFT_LIST',          '`' . $prefix . 'list`');
define('TABLE_KEVIN_SOFT_FILE',          '`' . $prefix . 'file`');
define('TABLE_KEVIN_SOFT_VERSION',          '`' . $prefix . 'version`');
define('TABLE_KEVIN_SOFT_GROUPVERSION',          '`' . $prefix . 'groupversion`');
define('TABLE_KEVIN_SOFT_MODULE',          '`' . $prefix . 'module`');

$config->objectTables['kevinsoft']        = TABLE_KEVIN_SOFT_LIST;
$config->objectTables['kevinsoftfile']  = TABLE_KEVIN_SOFT_FILE;
$config->objectTables['kevinsoftversion']    = TABLE_KEVIN_SOFT_VERSION;
$config->objectTables['kevinsoftgroup']    = TABLE_KEVIN_SOFT_GROUPVERSION;
$config->objectTables['kevinsoftmodule']    = TABLE_KEVIN_SOFT_MODULE;