<?php

$config->kevindevice              = new stdclass();

$config->kevindevice->nameRequire  = new stdclass();
$config->kevindevice->nameRequire->requiredFields  = 'name';

$config->kevindevice->devedit  = new stdclass();
$config->kevindevice->devedit->requiredFields  = 'name,type,status';
$config->kevindevice->devcreate  = new stdclass();
$config->kevindevice->devcreate->requiredFields  = 'name';
$config->kevindevice->groupcreate  = new stdclass();
$config->kevindevice->groupcreate->requiredFields= 'name,type';

$config->kevindevice->times        = new stdclass();
$config->kevindevice->times->begin = 0;
$config->kevindevice->times->end   = 24;
$config->kevindevice->times->delta = 10;

$config->kevindevice->confirmDelete = true;

$config->kevindevice->editor = new stdclass();
//$config->kevindevice->editor->devcreate   = array('id' => 'desc', 'tools' => 'simpleTools');
//$config->kevindevice->editor->devedit     = array('id' => 'desc', 'tools' => 'simpleTools');