<?php
/**
 * The batch edit view of todo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     todo
 * @version     $Id: create.html.php 2741 2012-04-07 07:24:21Z areyou123456 $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['todo']);?></span>
    <strong><small class='text-muted'><?php echo html::icon($lang->icons['batchEdit']);?></small> <?php echo $lang->todo->common . $lang->colon . $lang->todo->batchEdit;?></strong>
  </div>
</div>

<form class='form-condensed' method='post' target='hiddenwin' action='<?php echo $this->inlink('batchEdit', "from=todoBatchEdit");?>'>
  <table class='table table-form table-fixed'>
    <thead>
      <tr>
        <th class='w-40px'>   <?php echo $lang->idAB;?></th> 
        <th class='w-100px'>  <?php echo $lang->todo->date;?></th>
        <th class='w-80px'>   <?php echo $lang->todo->pri;?></th>
        <th class='red'>	  <?php echo $lang->todo->name;?></th>
		<th class='w-120px'>  <?php echo $lang->todo->hourstype;?></th>
		<th class='w-90px'>  <?php echo $lang->todo->project;?></th>
		<th class='w-180px'>  <?php echo $lang->kevinhours->projectName;?></th>
		<th class='w-80px'>  <?php echo $lang->todo->minutes;?></th>
        <th class='w-150px'>  <?php echo $lang->todo->beginAndEnd;?></th>
        <th class='w-100px'>   <?php echo $lang->todo->status;?></th>
      </tr>
    </thead>
    <?php foreach($editedTodos as $todo):if(!$this->todo->timeVerificated($todo->date, $this->config->todo->limitDate)) continue;?>
    <tr class='text-center'>
      <td><?php echo $todo->id . html::hidden("todoIDList[$todo->id]", $todo->id);?></td>
      <td><?php $limitDate=$this->config->todo->limitDate;echo html::input("dates[$todo->id]", $todo->date, "onchange='timeVerificated(this.value, $limitDate, $todo->id)'; class='form-control form-date'");?></td>
		 <td><?php echo html::select("pris[$todo->id]", $lang->todo->priList, $todo->pri, 'class=form-control');?></td>
      	<td style='overflow:visible'>
        <?php 
          echo html::input("names[$todo->id]", $todo->name, "class='form-control'"); ;
        if($todo->type == 'task')
        {
          echo  html::select("tasks[$todo->id]", $tasks, $todo->idvalue, 'class="form-control chosen"');
        }
        elseif($todo->type == 'bug')
        {
          echo  html::select("bugs[$todo->id]", $bugs, $todo->idvalue, 'class="form-control chosen"');
        }
        ?>
        </div>
      </td>
		 <td><?php echo html::select("hourstypes[$todo->id]", $lang->todo->hoursTypeList, $todo->hourstype, "onchange=\"setProjectForHoliday($todo->id)\"' class='form-control'");?></td>
		 <td><?php echo html::input("project[$todo->id]", $todo->project, "onkeyup='onChangeProject($todo->id)' rows='1' class='form-control'");?></td>
		 <td id=<?php echo 'projectNameBox'.$todo->id;?>><?php echo html::select("projectName[$todo->id]", $projectsArray, $todo->project, "onchange='onChangeProjectName($todo->id)' class='form-control'");?></td>
		<td><?php echo html::input("minutes[$todo->id]", $todo->minutes, "onchange=\"onChangeMinutes($todo->id)\" rows='1' class='form-control'");?></td>
      <td>
        <div class='input-group'>
          <?php echo html::select("begins[$todo->id]", $times, $todo->begin, "onchange=\"onChangeBefore($todo->id)\" class=\"form-control\" style=\"width: 50%\"") . html::select("ends[$todo->id]", $times, $todo->end, "onchange=\"onChangeNext($todo->id)\"  class=\"form-control\" style=\"width: 50%\"");?>
        </div>
      </td>
      <td><?php echo html::select("status[$todo->id]", $lang->todo->statusList, $todo->status, "class='form-control'");?></td>
    </tr>  
    <?php endforeach;?>
    <?php if(isset($suhosinInfo)):?>
    <tr><td colspan='9'><div class='text-left text-info'><?php echo $suhosinInfo;?>fdsafsdf</div></td></tr>
    <?php endif;?>
    <tfoot>
      <tr><td colspan='9'><?php echo html::submitButton();?></td></tr>
    </tfoot>
  </table>
</form>
<?php 
include '../../common/view/footer.html.php';
//获得上月考勤修改的截止时间为本月的某天某时
$endDay = $this->loadModel('kevinhours')->getLockedDayOfLastMonth();
// load the js for current page.
$this->loadModel('kevincom');
$js1 = $this->kevincom->getModuleFileContents('todo','./ext/js/kevinhours.js');
js::execute($js1);
?>
<script language='Javascript'>
var batchCreateNum = '<?php echo $config->todo->batchCreate;?>';
var todoTimesDelta= <?php echo "" +$config->todo->times->delta;?>;
var todoWorkStart = '<?php echo $config->todo->times->todoWorkStart;?>';
var todoWorkEnd = '<?php echo $config->todo->times->todoWorkEnd;?>';
var todoEatingStart = '<?php echo $config->todo->times->todoEatingStart;?>';
var todoEatingEnd = '<?php echo $config->todo->times->todoEatingEnd;?>';

var todoEndDay = <?php echo $endDay;?>;
var todoEndTime = <?php echo "'" . $this->config->todo->endTime . "'";?>;

var todoHoursCcolorHol = '<?php echo $config->todo->fontColor['hol'];?>';
var todoHoursCcolorOve = '<?php echo $config->todo->fontColor['ove'];?>';
var projectForHoliday= <?php echo "" +$config->todo->projectForHoliday;?>;
setProjectForHoliday();
timeVerificated();
</script>