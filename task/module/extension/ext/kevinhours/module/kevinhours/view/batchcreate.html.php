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
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php';

$worktype = 'task'; //default

?>

<form id = 'form1' class='form-condensed' method='post' target='hiddenwin'  onsubmit="return submitTest();">
	<div id='titlebar'>
		<div class='heading'>
			<span class='prefix pull-left'><?php echo html::icon($lang->icons['todo']); ?></span>
			<strong class='pull-left'><small class='text-muted'><?php echo html::icon($lang->icons['batchCreate']); ?></small> <?php echo $lang->kevinhours->batchcreate; ?>&nbsp;</strong>
			<div class='input-group w-200px pull-left' id='datepicker'>
				<span class='input-group-addon'><?php echo $lang->kevinhours->date; ?></span>
				<?php echo html::input('date', $date, "class='form-control form-date' onchange='changeBatchCreateDate(this.value)'"); ?>
			</div>
		</div>
	</div>
  <div>
	<table class='table table-fixed k-batch-table' style="width:100%; max-width: 1200px; min-width: 500px;">
		<thead>
			<tr>
				<th class='w-50px'><?php echo $lang->idAB; ?></th>
				<th class='w-p20'><?php echo $lang->kevinhours->name; ?></th>
				<th class='w-70px'><?php echo $lang->kevinhours->hourstype; ?></th>
				<th class='w-p10'><?php echo $lang->kevinhours->projectId; ?></th>
				<th class='w-p20'><?php echo $lang->kevinhours->projectName; ?></th>
				<th class='w-50px'><?php echo $lang->kevinhours->workhours; ?></th>
				<th class='w-p10'><?php echo $lang->todo->type; ?></th>
				<th class='w-220px' colspan="3"><?php echo $lang->kevinhours->beginAndEnd; ?></th>
			</tr>
		</thead>
		<?php $pri = 3; ?>
		<?php
		$lastEndTime = 0;
		$startTime;
		foreach ($todos as $todo):
			$tempEnd = str_replace(':', '', $todo->end);
			if ($tempEnd > $lastEndTime)
				$lastEndTime = $tempEnd;
			?>
			<tr class='text-center'>
				<td><?php echo $todo->id; ?></td>
				<td class='text-left'><?php echo html::a($this->createLink('todo', 'view', "id=$todo->id&from=my", '', true), $todo->name, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->kevinhours->view . "' data-icon='check'"); ?></td>
				<td><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
				<td><?php echo $todo->project; ?></td>
				<td><?php echo $this->kevincom->getProjectNameByProject($todo->project); ?></td>
				<td><?php echo $this->kevinhours->showWorkHours($todo->minutes); ?></td>
				<td><?php echo  $lang->todo->typeList[$todo->type]; ?></td>
				<td class='text-center'><?php echo $todo->begin; ?></td>
                <td class='text-center'><?php echo $todo->end; ?></td>
                <td class='text-center <?php echo $todo->status; ?>'><?php echo $lang->kevinhours->statusList[$todo->status]; ?></td>
			</tr>
<?php endforeach; ?>

<?php for ($i = 0; $i < $config->kevinhours->batchcreate; $i++): ?>
			<tr class='text-center'>
				<td><i class="icon-plus-sign"></i></td>
				<td class='text-left' style='overflow:visible'>
					<div class='<?php echo "nameBox" . ($i + 1); ?>'><?php echo html::input("names[$i]", '', 'class="form-control"'); ?></div>
				</td>
				<td><?php echo html::select("hourstypes[$i]", $lang->kevinhours->hoursTypeList, '', "onchange=\"setProjectForHoliday($i)\"' class='form-control'"); ?></td>
				<td><?php echo html::input("project[$i]", '', "onkeyup='onChangeProject($i)' rows='1' class='form-control'"); ?></td>
				<td id=<?php echo 'projectNameBox' . $i; ?>><?php echo html::select("projectName[$i]", $projectsArray, '', "onchange='onChangeProjectName($i)' class='form-control'"); ?></td>
				<td><?php echo html::input("minutes[$i]", '', "onchange=\"setBeginsAndEndsKevin($i, 'minutes');\" rows='1' class='form-control'"); ?></td>
				<td><?php echo html::select("types[$i]", $lang->todo->typeList, $worktype, " class='form-control'"); ?></td>
				<td><?php echo html::select("begins[$i]", $times, $lastEndTime, "onchange=\"setBeginsAndEndsKevin($i, 'begin');\" class='form-control'");?></td>
				<td><?php echo html::select("ends[$i]", $times, '', "onchange=\"setBeginsAndEndsKevin($i, 'end');\" class='form-control'");?></td>
				<td><?php echo html::select("status[$i]", $statusList, 'done', "class='form-control' style = 'min-width:70px'");?></td>
			</tr>  
<?php endfor; ?>
		<tfoot>
			<?php $column = 9; ?>
			<tr>
				<td colspan=<?php echo $column; ?>> 
					<div class="input-group" id="datepicker11">
<?php echo html::submitButton() . html::backButton(); ?>	
						<span class="input-group-addon"><input type="checkbox" name="holidayType" value='holidayType' <?php if (1 == $dateStatus) echo "checked=checked"; ?> onchange="onChangeHolidayType()"></input>节假日</span>
						间隔：<button id="button2hours" class="btn" type="button" value="2"  onclick="onButtonDefaulthours(2)">2小时</button>
						<button id="button4hours" class="btn" type="button" value="4"  onclick="onButtonDefaulthours(4)">4小时</button>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
  </div>
</form>
<?php
include '../../common/view/footer.html.php';
//获得上月考勤修改的截止时间为本月的某天某时
$endDay = $this->loadModel('kevinhours')->getLockedDayOfLastMonth();
?>
<script language='Javascript'>

	var isOnlyBody = <?php echo ($isonlybody == null) ? "false" : "true"; ?>;
	var limitDate = <?php echo $this->config->kevinhours->limitDate; ?>;
	var batchCreateNum = '<?php echo $config->kevinhours->batchcreate; ?>';
	var todoTimesDelta = <?php echo "" + $config->kevinhours->times->delta; ?>;
	var todoWorkStart = '<?php echo $config->kevinhours->times->todoWorkStart; ?>';
	var todoWorkEnd = '<?php echo $config->kevinhours->times->todoWorkEnd; ?>';
	var todoEatingStart = '<?php echo $config->kevinhours->times->todoEatingStart; ?>';
	var todoEatingEnd = '<?php echo $config->kevinhours->times->todoEatingEnd; ?>';

	var todoHoursCcolorHol = '<?php echo $config->kevinhours->fontColor['hol']; ?>';
	var todoHoursCcolorOve = '<?php echo $config->kevinhours->fontColor['ove']; ?>';
	var isHoliday = false;
	var projectForHoliday = <?php echo "" + $config->kevinhours->projectForHoliday; ?>;

	var todoEndDay = <?php echo $endDay; ?>;
	var todoEndTime = <?php echo "'" . $this->config->kevinhours->endTime . "'"; ?>;

	timeVerificated();
	setBeginsAndEndsKevin();
	onChangeHolidayType();
//getThisDayholiday();
	InitialBeginsSelect(true);
	setProjectForHoliday();
	function submitTest()
	{
		var isError = false;
		var objBegin;
		for (j = 0; j < batchCreateNum; j++)
		{
			var tt1 = document.getElementById('names[' + j + ']');
			if (tt1.value != "")
			{
				var value = document.getElementById('project[' + j + ']').value;
				if (value == "")
				{
					isError = true;
				}
			}
		}

		if (isError)
		{
			alert("填入了名称，但没填项目号！请填写");
			return false;
		}
		else
		{
			InitialBeginsSelect(false);
			document.form1.submit();
			InitialBeginsSelect(true);
		}
		return true;
	}
</script>

