<?php

//db define
define('TABLE_HOURSPROJECT', '`' . $config->db->prefix . 'hoursproject`');
define('TABLE_HOURSCASHCODE', '`' . $config->db->prefix . 'hourscashcode`');
define('TABLE_KEVINCLOCKACT', '`' . $config->db->prefix . 'kevinclockact`');

$config->objectTables['hoursproject']	 = TABLE_HOURSPROJECT;
$config->objectTables['hourscashcode']	 = TABLE_HOURSCASHCODE;
$config->objectTables['kevinclockact']	 = TABLE_KEVINCLOCKACT;
