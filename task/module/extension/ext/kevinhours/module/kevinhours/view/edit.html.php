<?php
/**
 * The create view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     todo
 * @version     $Id: edit.html.php 4728 2013-05-03 06:14:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
 <?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div class='container mw-700px'>
  <div id='titlebar'>
    <div class='heading'>
      <span class='prefix'><?php echo html::icon($lang->icons['todo']);?> <strong><?php echo $todo->id;?></strong></span>
      <strong><?php echo html::a($this->createLink('kevinhours', 'view', 'todo=' . $todo->id), $todo->name);?></strong>
      <small class='text-muted'> <?php echo $lang->todo->edit;?></small>
    </div>
  </div>

  <form class='form-condensed' method='post' target='hiddenwin' id='dataform' onsubmit="return isCommentEmpty();">
    <table class='table table-form'> 
      <tr>
        <th class='w-80px'><?php echo $lang->kevinhours->date;?></th>
        <td class='w-p25-f'>
          <div class='input-group'>
            <?php 
				if(common::hasPriv('kevinhours', 'modifyOtherHours'))
				{
					echo html::input('date', $todo->date, "class='form-control form-date'");
				}
				else
				{
					$limitDate=$this->config->kevinhours->limitDate;
					echo html::input('date', $todo->date, "onchange='timeVerificated(this.value, $limitDate)' class='form-control form-date'");
				}
			?>
           
          </div>
        </td>
		<th class='w-80px'><?php echo $lang->todo->pri;?></th>
        <td><?php echo html::select('pri', $lang->todo->priList, $todo->pri, "class='form-control'");?></td>
      </tr>
	  <tr>
        <th><?php echo $lang->todo->type;?></th>
		<td><?php echo html::select('type', $lang->todo->typeList, $todo->type, "class='form-control'");?></td>
        <th><?php echo $lang->kevinhours->hourstype;?></th>
		<td><?php echo html::select('hourstype', $lang->kevinhours->hoursTypeList, $todo->hourstype, "onchange=setProjectForHoliday(); class='form-control'");?></td>
      </tr>
	  <tr>
        <th><?php echo $lang->kevinhours->projectId;?></th>
        <td><?php echo html::input('project', $todo->project, 'onkeyup=onChangeProject() class=form-control');?></td>
		<th><?php echo $lang->kevinhours->projectName;?></td>
		<td  id='projectNameBox'><?php echo html::select('projectName', $projectsArray, $todo->project, 'onchange=onChangeProjectName(); class=form-control');?></td>
      </tr>
      <tr>
        <th><?php echo $lang->todo->name;?></th>
        <td colspan='3'><?php echo html::input('name', $todo->name, " class=form-control");?></td>
		<th></th>
		<td></td>
      </tr>  
      <tr>
        <th><?php echo $lang->kevinhours->workhours;?></th>
        <td>
			<?php $time = (int)(($todo->minutes)/60) . ':' . (int)(($todo->minutes)%60)?>
			<div class='input-group'>
            <?php echo html::input('minutes', $time, "onchange=onChangeMinutes(); class='form-control form-hour'");?>
          </div>
        </td>
        <th><?php echo $lang->kevinhours->beginAndEnd;?></th>
        <td>
          <div class='input-group'>
            <?php echo html::select('begin', $times, $todo->begin, 'onchange=onChangeBefore(); class="form-control" style="width: 50%"') . html::select('end', $times, $todo->end, 'onchange=onChangeNext(); class="form-control" style="width: 50%"');?>
          </div>
        </td>
      </tr>  
	  <tr>
        <th><?php echo $lang->kevinhours->status;?></th>
        <td><?php echo html::select('status', $lang->kevinhours->statusList, $todo->status, "class='form-control'");?></td>
		<th></th>
        <td><input type='checkbox' id='dateSwitcher' onclick='switchDateFeature(this);' disabled="disabled" style="display:none"> </input></td>
      </tr>
      <tr>
		<?php 
			if($comment)
			{
				echo '<th>' . $lang->kevinhours->comment . '</th>';
				echo '<td>' . html::textarea('comment', '',"rows='1' class='w-p100'") . "</td>";
			}
			else echo "<td></td>";
		?>
        <td>
          <?php echo html::submitButton() . html::backButton();?>
        </td>
      </tr>
	  <tr>
        <th><?php echo $lang->kevinhours->desc;?></th>
        <td colspan='3'><?php echo html::textarea('desc', htmlspecialchars($todo->desc), "rows=8 class=area-1");?></td>
      </tr>
    </table>
  </form>
</div>
<?php include '../../common/view/footer.html.php';
//获得上月考勤修改的截止时间为本月的某天某时
$endDay = $this->loadModel('kevinhours')->getLockedDayOfLastMonth();
// load the js for current page.
$this->loadModel('kevincom');
$js1 = $this->kevincom->getModuleFileContents('todo','./ext/js/kevinhours.js');
js::execute($js1);
?>

<script language='Javascript'>
switchDateFeature(document.getElementById('dateSwitcher'));
var todoTimesDelta= <?php echo "" +$config->kevinhours->times->delta;?>;
var projectForHoliday= <?php echo "" +$config->kevinhours->projectForHoliday;?>;

var todoEndDay = <?php echo $endDay;?>;
var todoEndTime = <?php echo "'" . $this->config->kevinhours->endTime . "'";?>;

timeVerificated();

setProjectForHoliday();
function isCommentEmpty()
{
	//判断备注是否填写
	var commentObj = document.getElementById("comment");
	if(commentObj != null)
	{
		comment = commentObj.value;
		if('' == comment)
		{
			alert("考勤修改备注必须填写！");
			return false;
		}
	}
	return true;
}
</script>