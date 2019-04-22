<?php
/**
 * The create view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class='container mw-1400px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix'><?php echo html::icon($lang->icons['todo']);?></span>
      <strong><small class='text-muted'><?php echo html::icon($lang->icons['create']);?></small> <?php echo $lang->kevinhours->create;?></strong>
    </div>
  </div>
  <?php $lastEndTime= 0; $startTime;
	foreach($todos as $todo):
        $tempEnd  = str_replace(':', '', $todo->end);
		if($tempEnd>$lastEndTime) $lastEndTime = $tempEnd;
	endforeach;
	if($lastEndTime == 0) $lastEndTime= '0800';//如果当天未有代办,起始时间设为8点
	?>
  <form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
    <table class='table table-form'> 
      <tr>
        <th class='w-80px'><?php echo $lang->kevinhours->date;?></th>
        <td class='w-p25-f'>
          <div class='input-group'>
            <?php echo html::input('date', $date, "onchange='changeCreateDate(this.value)' class='form-control form-date' ");?>
		</div></td>
		<th><table><tr><td><?php echo $lang->kevinhours->hourstype;?></td>
		<td><?php echo html::select('hourstype', $lang->kevinhours->hoursTypeList
			, '', 'onchange=setProjectForHoliday(); class=form-control');?></td>
        <td><?php echo $lang->todo->type;?></td>
		<td><?php echo html::select('type', $lang->todo->typeList
			, 'design', 'class=form-control');?></td>
        </tr></table></th>

      </tr>
		<?php if(!$config->kevinhours->ListSimpleModel){ ?>
			<tr>
        <th><?php echo $lang->kevinhours->type;?></th>
        <td><?php echo html::select('type', $lang->todo->typeList, '', 'onchange=loadList(this.value); class=form-control');?></td>
      </tr>
	  <tr>
        <th><?php echo $lang->kevinhours->pri;?></th>
        <td><?php echo html::select('pri', $lang->todo->priList, '', "class='form-control'");?></td>
      </tr>
		<?php }?>
      
		<tr>
        <th><?php echo $lang->kevinhours->projectId;?></th>
        <td><?php echo html::input('project', '', 'onkeyup=onChangeProject() class=form-control');?></td>
		<th><table><tr><td><?php echo $lang->kevinhours->projectName;?></td><td  id='projectNameBox'>
			<?php echo html::select('projectName', $projectsArray, '', 'onchange=onChangeProjectName(); class=form-control');?></td><td><?php
		if(common::hasPriv('todo', 'searchProject'))
		{
			echo html::a(helper::createLink('kevinhours', 'searchproject', '', '', true), $lang->kevinhours->searchproject, '', "class='btn export'");
		}
		?></td></tr></table></th>
      </tr>
      <tr>
        <th><?php echo $lang->kevinhours->name;?></th>
        <td colspan='2'>
          <div class='nameBox'><?php echo html::input('name', $name, 'class=form-control');?></div>
          </td>
      </tr>
			<tr>
        <th><?php echo $lang->kevinhours->desc;?></th>
        <td colspan='2'><?php echo html::textarea('desc', '', "rows='8' class='form-control'");?></td>
      </tr>  
      <tr>
        <th><?php echo $lang->kevinhours->status;?></th>
        <td><?php echo html::select('status', $lang->kevinhours->statusList, 'wait', "class='form-control'");?></td>
		<th><table><tr><td><?php echo $lang->kevinhours->minutes;?></td>
		<td>
          <div class='input-group'>
            <?php echo html::input('minutes','2:00', "onchange=onChangeMinutes(); class='form-control'");?>
          </div>
		  </td></tr></table></th>
	  </tr>
      <tr>
        <th><?php echo $lang->kevinhours->beginAndEnd;?></th>
        <td>
          <div class='input-group'>
            <?php echo html::select('begin', $times, $lastEndTime, 'onchange=onChangeBefore(); class="form-control" style="width: 50%;"') . html::select('end', $times, '', 'onchange=onChangeNext(); class="form-control" style="width: 50%; margin-left:-1px"');?>
          </div>
        </td>
        <td></td>
      </tr> 
	  <?php if(!$config->kevinhours->ListSimpleModel){ ?>
		<tr>
        <th><?php echo $lang->todo->private;?></th>
        <td><input type='checkbox' name='private' id='private' value='1'></td>
      </tr>
		<?php }?>
        
      <tr>
        <td></td>
        <td colspan='2' class='text-center'>
          <?php echo html::submitButton() . html::backButton();?>
        </td>
      </tr>
    </table>
  </form>
</div>

<?php include '../../common/view/footer.html.php';
//获得上月考勤修改的截止时间为本月的某天某时
$endDay = $this->kevinhours->getLockedDayOfLastMonth();
// load the js for current page.
$js1 = $this->kevincom->getModuleFileContents('todo','./ext/js/kevinhours.js');
js::execute($js1);
?>
<script language='Javascript'>
var limitDate = <?php echo $this->config->kevinhours->limitDate; ?>;
var todoTimesDelta= <?php echo "" +$config->kevinhours->times->delta;?>;
var projectForHoliday= <?php echo "" +$config->kevinhours->projectForHoliday;?>;
var todoEndDay = <?php echo $endDay;?>;
var todoEndTime = <?php echo "'" . $config->kevinhours->endTime . "'";?>;

timeVerificated();
onChangeBefore();
setProjectForHoliday();
</script>

