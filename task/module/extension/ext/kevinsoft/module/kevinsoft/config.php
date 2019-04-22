<?php
/**
 * The config file
 *
 * @copyright   Kevin
 * @charge: free
 * @license: ZPL (http://zpl.pub/v1)
 * @author      Kevin <3301647@qq.com>
 * @package     kevinsoft
 * @link        http://www.zentao.net
 */
?>
<?php
$config->kevinsoft = new stdclass();

$config->kevinsoft->filecreate = new stdclass();
$config->kevinsoft->filecreate->requiredFields = 'soft,version,title';//录入时必须的项

$config->kevinsoft->fileedit = new stdclass();
$config->kevinsoft->fileedit->requiredFields = 'title';//录入时必须的项

$config->kevinsoft->groupversioncreate = new stdclass();
$config->kevinsoft->groupversioncreate->requiredFields = 'version,file';//录入时必须的项
$config->kevinsoft->groupversionedit = new stdclass();
$config->kevinsoft->groupversionedit->requiredFields = 'version,groupversion';//录入时必须的项
$config->kevinsoft->versioncreate = new stdclass();
$config->kevinsoft->versioncreate->requiredFields = 'soft,version';//录入时必须的项
$config->kevinsoft->versionedit = new stdclass();
$config->kevinsoft->versionedit->requiredFields = 'version';//录入时必须的项
$config->kevinsoft->softcreate = new stdclass();
$config->kevinsoft->softcreate->requiredFields = 'soft,version';//录入时必须的项
$config->kevinsoft->softedit = new stdclass();
$config->kevinsoft->softedit->requiredFields = 'name';//录入时必须的项
$config->kevinsoft->modulecreate = new stdclass();
$config->kevinsoft->modulecreate->requiredFields = 'module';//录入时必须的项
$config->kevinsoft->moduledit = new stdclass();
$config->kevinsoft->moduledit->requiredFields = 'module';//录入时必须的项
//
$config->kevinsoft->fileupdate = new stdclass();
$this->config->kevinsoft->fileupdate->requiredFields =  'name,code';
$config->kevinsoft->softupdate = new stdclass();
$this->config->kevinsoft->softupdate->requiredFields =  'name,code';
$config->kevinsoft->versionupdate = new stdclass();
$this->config->kevinsoft->versionupdate->requiredFields =  'name,code';

$config->kevinsoft->editor = new stdclass();
$config->kevinsoft->editor->versionedit   = array('id' => 'comment', 'tools' => 'simpleTools');