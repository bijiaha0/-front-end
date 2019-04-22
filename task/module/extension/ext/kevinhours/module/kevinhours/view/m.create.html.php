<?php
/**
 * The batch create view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/m.header.html.php';?>
<?php 
//获得上月考勤修改的截止时间为本月的某天某时
$endDay = $this->kevinhours->getLockedDayOfLastMonth();
?>
<?php $lastEndTime= 0; $startTime;
	foreach($todos as $todo):
        $tempEnd  = str_replace(':', '', $todo->end);
		if($tempEnd>$lastEndTime) $lastEndTime = $tempEnd;
	endforeach;
	if($lastEndTime == 0) $lastEndTime= '0800';//如果当天未有代办,起始时间设为8点
	?>
</div>
<form class='form-condensed' method='post' target='hiddenwin'>
  <?php
	if($begin != '') $lastEndTime = $begin;
	if($end == '') $end = $this->kevinhours->addDefaultTimeSag($lastEndTime);
	echo html::select('date', $datesArray, $date, "onchange=changeCreateDate(this.value);");
    echo html::input("name", $name, "placeholder='{$lang->kevinhours->name}'");
	echo html::select('project', $projectsArray, $project, 'onchange=onChangeProjectName(); class=form-control');
	echo html::select('hourstype', $lang->kevinhours->hoursTypeList, '', "onchange=loadList(this.value)");
	echo html::select('begin', $times, $lastEndTime, '');
	echo html::select('end', $times, $end, '');
	echo html::hidden('status', 'done');//手机端填写默认为完成
	?>
<p class='text-center'>
  <?php
  echo html::submitButton('', "data-inline='true' data-theme='b'");
  echo html::linkButton($lang->goback, $this->createLink('my', 'todo', "type={$this->session->todoType}"), 'self', "data-inline='true'");
  ?>
</p>
</form>
<script>
var limitDate = <?php echo $this->config->kevinhours->limitDate; ?>;
var todoEndDay = <?php echo $endDay;?>;
var todoEndTime = <?php echo "'" . $config->kevinhours->endTime . "'";?>;
</script>
<?php 
// load the js for current page.
$this->loadModel('kevincom');
$js1 = $this->kevincom->getModuleFileContents('kevinhours', './js/common.js');
js::execute($js1);
$js1 = $this->kevincom->getModuleFileContents('kevinhours', './js/create.js');
js::execute($js1);
include '../../common/view/m.footer.html.php';
?>
