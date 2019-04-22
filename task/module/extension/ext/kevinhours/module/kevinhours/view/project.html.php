<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/tablesorter.html.php'; ?>
<?php
include '../../common/view/datepicker.html.php';

$this->loadModel('todo');
?>
<div id='featurebar'>
    <ul class='nav'>
<?php if ($projectName): ?>
			<li class=""><a id="currentItem" href="javascript:showDropMenu('project', '100', 'kevinhours', 'project', '<?php echo "$type"; ?>', '<?php echo "$isShowDetail"; ?>')"><?php echo $projectName . '&nbsp;'; ?><span class="icon-caret-down"></span></a>
				<div id="dropMenu"><input class="form-control" id="search" value="" placeholder="搜索" type="text"></div>
			</li>
		<?php endif ?>
	<?php include './commontitlebar.html.php'; ?>
    </ul>
	<?php $state			 = '';
	if ('true' == $isShowDetail)
		$state			 = 'checked=checked';
	?>
	<div class='actions'><?php echo html::checkbox('isShowDetail', $lang->kevinhours->isShowDetail, 'checked', "$state onclick='changePageState(this);' 'class='form-control'"); ?></div>
</div>
<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='kevinhoursList'>
<?php //利用<thead>将表格的头部包含再include '../../common/view/tablesorter.html.php'; 那么将实现表格的排序且不刷新网页非常简单 ?>
	<thead>
		<tr class='text-center'>
			<th><font size='3'>姓名</font></th>
			<th><font size='3'>部门</font></th>
			<th><font size='3'>科室</font></th>
			<th><font size='3'>工号</font></th>
			<th><font size='3'>正常</font></th>
			<th><font size='3'>加班</font></th>
			<th><font size='3'>总计</font></th>
			<th><font size='3'>比例图</font></th>
		</tr>
	</thead>
	<?php
	//代办所有者数组
	$accountArray	 = array();
	$lastAccount	 = '';
	//遍历代办
	foreach ($allTodos as $todo):
		//获得代办所有者数组
		if ($lastAccount != $todo->account) {
			$lastAccount	 = $todo->account;
			$accountArray[]	 = $todo->account;
		}
	endforeach;
	?>
	<?php
	$accountTotalHoursArray	 = array();
	$accountNorHoursArray	 = array();
	$accountOveHoursArray	 = array();
	$realNameArray			 = array();
	$accountTotalHours		 = 0;
	$accountNorHours		 = 0;
	$accountOveHours		 = 0;
	//遍历科室成员
	foreach ($accountArray as $account) {
		$this->loadModel('kevincom');
		$realname				 = $this->kevincom->getRealnameByAccount($account);
		$code					 = $this->kevincom->getUserCodeByAccount($account);
		$deptParentName			 = $this->kevincom->getDeptByAccount($account);
		$deptName				 = $this->kevincom->getDeptNameByAccount($account);
		$norHours				 = 0;
		$oveHours				 = 0;
		$totalHours				 = 0;
		$projectTotalHours		 = 0;
		$realNameArray[$account] = $realname;
		echo "<tr height='19'>";
		echo "<td class='text-center'><font size='4'>" . html::a($this->createLink('kevinhours', 'index', "type=$type&account=$account"), $realname, '') . "</font></td>";
		echo "<td align='center'><font size='3'>$deptParentName</font></td>";
		echo "<td align='center'><font size='3'>$deptName</font></td>";
		echo "<td align='center'><font size='3'>$code</font></td>";

		//遍历考勤
		foreach ($allTodos as $todo):
			$projectTotalHours += $todo->minutes;
			if ($todo->account == $account) {
				if ($todo->hourstype == 'nor') {
					$norHours+=$todo->minutes;
					$totalHours+=$todo->minutes;
					$accountNorHours += $todo->minutes;
				} else if ($todo->hourstype == 'ove') {
					$oveHours+=$todo->minutes;
					$totalHours+=$todo->minutes;
					$accountOveHours += $todo->minutes;
				}
			}
		endforeach;
		$accountTotalHours += $totalHours;
		if (array_key_exists($account, $accountTotalHoursArray))
			$accountTotalHoursArray[$account] += $totalHours;
		else
			$accountTotalHoursArray[$account]	 = $totalHours;
		if (array_key_exists($account, $accountNorHoursArray))
			$accountNorHoursArray[$account] += $norHours;
		else
			$accountNorHoursArray[$account]		 = $norHours;
		if (array_key_exists($account, $accountOveHoursArray))
			$accountOveHoursArray[$account] += $oveHours;
		else
			$accountOveHoursArray[$account]		 = $oveHours;
		$totalPercent						 = ($totalHours / $projectTotalHours) * 100 . '%';
		$norHours							 = $this->kevinhours->showHours($norHours);
		$oveHours							 = $this->kevinhours->showHours($oveHours);
		$totalHours							 = $this->kevinhours->showHours($totalHours);
		echo "<td align='center'><font size='3'>$norHours</font></td>";
		echo "<td align='center'><font size='3'>$oveHours</font></td>";
		echo "<td align='center'><font size='3'>$totalHours</font></td>";
		echo "<td class='text-left'><svg width=$totalPercent height='20' version='1.1'><rect x='0' y='0' width=100% height=100% style='fill:green'/></svg></td>";
		echo "</tr>";
	}
	?>
</table>
<table class='table table-condensed table-hover table-striped tablesorter table-fixed' id='kevinhoursList'>
	<tr class='text-center'>
		<th><font size='3'>合计</font></th>
		<th></th>
		<th></th>
		<th></th>
		<th><font size='3'>
			<?php echo $this->kevinhours->showHours($accountNorHours, 1); ?>
			</font></th>
		<th><font size='3'>
			<?php echo $this->kevinhours->showHours($accountOveHours, 1); ?>
			</font></th>
		<th><font size='3'>
<?php $projectTotalHours = $this->kevinhours->showHours($accountTotalHours, 1);
echo $projectTotalHours;
?>
			</font></th>
		<th></th>
	</tr>
</table>
<?php if ($pager != null) { ?>
	<br><br>
	<table class="table table-condensed table-hover" id='todoList'>
		<tr class='text-center'>
			<th class='w-id'>    <?php echo $lang->idAB; ?></th>
			<th class='w-date'>  <?php echo $lang->kevinhours->realname; ?></th>
			<th class='w-date'>  <?php echo $lang->kevinhours->date; ?></th>
			<th>                 <?php echo $lang->kevinhours->name; ?></th>
			<th class='w-100px'> <?php echo $lang->kevinhours->hourstype; ?></th>
			<th class='w-80px'>  <?php echo $lang->kevinhours->hours; ?></th>
			<th class='w-hour'>  <?php echo $lang->kevinhours->beginAB; ?></th>
			<th class='w-hour'>  <?php echo $lang->kevinhours->endAB; ?></th>
		</tr>
		<tbody>
	<?php $id = 0;
	foreach ($todos as $todo):$id+=1;
		?>
				<tr class='text-center'>
					<td class='text-left'><?php echo $id; ?></td>
					<td class='text-left'><?php echo $todo->realname; ?></td>
					<td><?php echo $todo->date == '2030-01-01' ? $lang->kevinhours->periods['future'] : $todo->date; ?></td>
					<td class='text-left'><?php echo html::a($this->createLink('todo', 'view', "id=$todo->id&from=my", '', true), $todo->name, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->kevinhours->view . "' data-icon='check'"); ?></td>
					<td style="background-color:<?php echo $config->kevinhours->fontColor[$todo->hourstype]; ?>"><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
					<td><?php echo $this->kevinhours->showWorkHours($todo->minutes); ?></td>
					<td><?php echo $todo->begin; ?></td>
					<td><?php echo $todo->end; ?></td>
				</tr>
	<?php endforeach; ?>
		</tbody>
		<tfoot><tr><td colspan='8'><?php $pager->show(); ?></td></tr></tfoot>
	</table>
<?php } ?>
<?php include '../../common/view/footer.html.php'; ?>
<script>
	var currentProjectID = '<?php echo $projectID; ?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth; ?>';
	var methodName = '<?php echo 'project'; ?>';
	function changeDate(date)
	{
		var projectID = <?php echo $projectID; ?>;
		date = date.replace(/\-/g, '');
		link = createLink('kevinhours', 'project', 'projectID=' + projectID + '&type=' + date);
		location.href = link;
	}
	function changePageState(switcher)
	{
		var projectID = <?php echo $projectID; ?>;
		var type = "<?php echo $type; ?>";
		var link;
		if (switcher.checked)
		{
			link = createLink('kevinhours', 'project', 'projectID=' + projectID + '&type=' + type + '&isShowDetail=true');
		}
		else
		{
			link = createLink('kevinhours', 'project', 'projectID=' + projectID + '&type=' + type + '&isShowDetail=false');
		}
		location.href = link;
	}
</script>
