<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php if (common::hasPriv('kevinhours', 'checkAll')) { ?>
	<div class='side'>
		<a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
		<div class='side-body'>
			<div class='panel panel-sm'>
				<div class='panel-heading nobr'><?php echo html::icon($lang->icons['company']); ?> <strong><?php echo $lang->dept->common; ?></strong></div>
				<div class='panel-body'>
	<?php echo $deptTree; ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<div class='main'>
	<form class='form-condensed' method='post' target='hiddenwin'>
		<table class='table table-form'>
			<tr>
				<th><?php echo $lang->kevinhours->certainYear; ?></th>
				<td><?php echo html::select('year', $yearList, $year, 'class=form-control'); ?></td>
				<td>
					<table width="100%">
						<tr>
							<td><b><?php echo $lang->kevinhours->certainMonth; ?></b></td>
							<td><?php echo html::select('month', $lang->kevinhours->month, $month, "onchange=setSeasonInputNull() class='form-control'"); ?></td>
							<td><b><?php echo $lang->kevinhours->certainSeason; ?></b></td>
							<td><?php echo html::select('season', $lang->kevinhours->season, $season, "onchange=setMonthInputNull() class='form-control'"); ?></td>
						</tr>
					</table>
				</td>
				<td width='120px'></td>
			</tr>
<?php if (common::hasPriv('kevinhours', 'checkAll') || common::hasPriv('kevinhours', 'browseDeptHours')) { ?>
				<tr>
					<th><?php echo $lang->kevinhours->dept; ?></th>
					<td><?php echo html::select('dept', $deptArray, $deptIndex, "onchange=getEmployee(this.value) class='form-control chosen'"); ?></td>	
					<td>
						<span id='userIdBox'><?php echo html::select('userIndex', $userArray, $userIndex, "class='form-control chosen'"); ?></span>
					</td>
				</tr>
<?php } ?>
			<tr>
				<th><?php echo $lang->kevinhours->hourstype; ?></th>
				<td><?php echo html::select('hourstype', $lang->kevinhours->exportHoursTypeList, $hourstypeIndex, 'class=form-control'); ?></td>
				<?php if (common::hasPriv('kevinhours', 'checkAll')) { ?>
					<td><?php echo html::select('employeetype', $lang->kevinhours->employeeTypeList, $employeetypeIndex, 'class=form-control'); ?></td>
<?php } ?>
			</tr>
			<?php if (common::hasPriv('kevinhours', 'browseDeptHours')||common::hasPriv('kevinhours', 'checkAll')){?>
			<tr>
				<th><?php echo $lang->kevinhours->containchild; ?></th>
				<td><?php echo html::select('deptcount', $lang->kevinhours->deptCountList, $deptcount, "class='form-control chosen'"); ?></td>
			</tr>
			<?php }?>
			<tr>
				<td colspan='2' class='text-center'><?php echo html::submitButton("提交") . html::backButton(); ?></td>
				<td >
					<table width="100%"><tr>
							<td class='w-120px'>
								<?php if (common::hasPriv('kevinhours', 'printover'))
									echo  html::checkbox('fillauth',$lang->kevinhours->fillauthlist['1'],'',"id='fillauth'");?>
							</td>
							<td class='text-right'>
								<?php
								if (common::hasPriv('kevinhours', 'printover')) {
									echo "<input class='btn' type='button' value='班组加班' onclick=printover();>";
								}
								?>
							</td>
							<td class='text-right'>
								<?php
								if (common::hasPriv('kevinhours', 'dispatched')) {
									echo "<input class='btn' type='button' value='外协工作' onclick=dispatched();>";
								}
								?>
							</td>
							<td class='text-right'>
								<?php
								if (common::hasPriv('kevinhours', 'printovertable')) {
									echo "<input class='btn' type='button' value='正式加班' onmouseover=setCurrentMonth('$currentMonth') onclick=printovertable();>";
								}
								?>
							</td>
							<td class='w-120px'><?php echo html::checkbox('isIncludeAnn', $lang->kevinhours->isIncludeAnn, $this->session->isIncludeAnn, ''); ?></td>
						</tr></table>
				</td>
			</tr>
		</table>
	</form>
	<form method='post' id='todoform'>
		<center>
			<table width="80%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="3" align="center"><font size='5'>班组人员工作情况一览</font></td>
				</tr>
				<tr>
					<td colspan="6" align="center"><font size='3'><?php echo $deptParentName . '-' . $deptName; ?></font></td>
				</tr>
			</table>
			<br/>
			<?php
			$totalHours			 = 0;
			$norHours			 = 0;
			$oveHours			 = 0;
			$holHours			 = 0;
			$norOveHours		 = 0;
			$WeekdayHours		 = 0;
			$lawOveHours		 = 0;
			$weightingHours		 = 0;
			$dateHoursArray		 = array();
			$allDateHoursArray	 = array();
			$currentIndex		 = '';
			foreach ($allTodos as $todo) {
				$currentIndex						 = $todo->account . ',' . $todo->date . ',' . $todo->hourstype;
				if (array_key_exists($currentIndex, $allDateHoursArray))
					$allDateHoursArray[$currentIndex] += $todo->minutes;
				else
					$allDateHoursArray[$currentIndex]	 = $todo->minutes;

				//是否存在工时不足8小时情况
				if ($todo->hourstype == $lang->kevinhours->hoursTypeNor || $todo->hourstype == $lang->kevinhours->hoursTypeHol || $todo->hourstype == $lang->kevinhours->hoursTypeAnn || $todo->hourstype == $lang->kevinhours->hoursTypeRep) {
					$index					 = $todo->account . ',' . $todo->date;
					if (array_key_exists($index, $dateHoursArray))
						$dateHoursArray[$index] += $todo->minutes;
					else
						$dateHoursArray[$index]	 = $todo->minutes;
				}
				$totalHours += $todo->minutes; //总时间
				if ($lang->kevinhours->hoursTypeNor == $todo->hourstype)
					$norHours += $todo->minutes; //正常时间
				if ($lang->kevinhours->hoursTypeOve == $todo->hourstype) {
					$oveHours += $todo->minutes; //加班时间
					if (array_key_exists($todo->date, $calendarArray)) {
						if ($lang->kevinhours->hoursTypeLaw == $calendarArray[$todo->date]) {
							$lawOveHours += $todo->minutes; //法定加班时间
						}
						else if ($lang->kevinhours->hoursTypeHol == $calendarArray[$todo->date]) {
							$WeekdayHours += $todo->minutes; //周末加班时间
						}
						else {
							$norOveHours += $todo->minutes; //平时加班时间
						}
					}
					else {
						if ((date('w', strtotime($todo->date)) == 6) || (date('w', strtotime($todo->date)) == 0)) {
							$WeekdayHours += $todo->minutes; //周末加班时间
						}
						else {
							$norOveHours += $todo->minutes; //平时加班时间
						}
					}

					$weightingHours = $norOveHours * 1.5 + $WeekdayHours * 2 + $lawOveHours * 3; //加班时间加权
				}
				if ($lang->kevinhours->hoursTypeHol == $todo->hourstype || $lang->kevinhours->hoursTypeAnn == $todo->hourstype)
					$holHours += $todo->minutes; //请假时间
			}
			?>
			<table width="80%" border="1" cellpadding="0" cellspacing="0">
				<col width="100"/>
				<col width="100"/>
				<col width="100" />
				<col width="100"/>
				<col width="100"/>
				<col width="100"/>
				<col width="100" />
				<col width="100" />
				<tr height="35">
					<td colspan="8" align="center"><font size='4'>请注意:以下统计为估算,请以实际计算为准.单位为/小时</font></td>
				</tr>
				<tr height="35">
					<td align="center"><font size='4'>总计</font></td>
					<td align="center"><font size='4'>正常</font></td>
					<td align="center"><font size='4'>加班</font></td>
					<td align="center"><font size='4'>请假</font></td>
					<td align="center"><font size='4'>平时加班</font></td>
					<td align="center"><font size='4'>周末加班</font></td>
					<td align="center"><font size='4'>法定加班</font></td>
					<td align="center"><font size='4'>加班加权时间</font></td>
				</tr>
				<tr height="35">
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($totalHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($norHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($oveHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($holHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($norOveHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($WeekdayHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($lawOveHours); ?></font></td>
					<td align="center"><font size='4'><?php echo $this->kevinhours->showHours($weightingHours); ?></font></td>
				</tr>
			</table>
			<br/>
			<table width="80%" border="1" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="5" align="center"><font size='4'>请注意:以下显示为异常考勤</font></td>
				</tr>
				<tr>
					<td align="center"><strong>序号</strong></td>
					<td align="center"><strong>用户名</strong></td>
					<td align="center"><strong>日期</strong></td>
					<td align="center"><strong>工作时间</strong></td>
					<td align="center">异常原因</td>
				</tr>
				<?php
				$itemIndex	 = 0;
				$tempArray	 = array();
				$tempAccount = '';
				$tempDate	 = '';
				foreach ($dateHoursArray as $currentDate => $currentHours) {
					if ($currentHours == 480)
						continue;
					$itemIndex += 1;
					$tempArray	 = explode(',', $currentDate);
					$tempAccount = $tempArray[0];
					$tempDate	 = $tempArray[1];
					$tempDate1	 = str_replace('-', '', $tempDate);
					?>
					<tr>
						<td align="center"><?php echo $itemIndex; ?></td>
						<td align="center"><?php echo $this->kevincom->getRealnameByAccount($tempAccount); ?></td>
						<td align="center"><?php echo html::a(helper::createLink('kevinhours', 'todo', "type=$tempDate1&account=$tempAccount"), $tempDate, '', "class='link'"); ?></td>
						<td align="center"><?php echo $this->kevinhours->showHours($currentHours); ?></td>
						<td align="center"><?php echo '当天正常工作时间不为8小时'; ?></td>
					</tr>
<?php } ?>
			</table>
			<br/>
			<table width="80%" border="1" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="5" align="center"><font size='4'>请注意:以下测试</font></td>
				</tr>
				<tr>
					<td align="center"><strong>序号</strong></td>
					<td align="center"><strong>用户名</strong></td>
					<td align="center"><strong>日期</strong></td>
					<td align="center"><strong>工作时间</strong></td>
					<td align="center">异常原因</td>
				</tr>
				<tr>
					<?php
					$allAccountArray	 = array();
					$allDateAccountArray = array();
					foreach ($allDateHoursArray as $currentDate => $currentHours) {
						$tempArray	 = explode(',', $currentDate);
						$tempAccount = $tempArray[0];
						$tempDate	 = $tempArray[1];
						$tempType	 = $tempArray[2];
						if (array_key_exists($tempDate, $allDateAccountArray)) {
							$tempAccountArray = $allDateAccountArray[$tempDate];
							if (!in_array($tempAccount, $tempAccountArray)) {
								$tempAccountArray[]				 = $tempAccount;
								$allDateAccountArray[$tempDate]	 = $tempAccountArray;
							}
						}
						else
							$allDateAccountArray[$tempDate]	 = array($tempAccount);
						if (!in_array($tempAccount, $allAccountArray))
							$allAccountArray[]				 = $tempAccount;
					}

					foreach ($calendarArray as $tempDay => $tempType) {
						if (!array_key_exists($tempDate, $allDateAccountArray))
							continue;
						$tempAccountArray = $allDateAccountArray[$tempDate]; //获得当前所有有考勤的人员

						if ($lang->kevinhours->hoursTypeLaw == $tempType || $lang->kevinhours->hoursTypeHol == $tempType) {
							foreach ($tempAccountArray as $tempAccount) {

								$tempIndex = $tempAccount . ',' . $tempDay . ',';
								if (array_key_exists($tempIndex . $lang->kevinhours->hoursTypeNor, $allDateAccountArray) || array_key_exists($tempIndex . $lang->kevinhours->hoursTypeHol, $allDateAccountArray) || array_key_exists($tempIndex . $lang->kevinhours->hoursTypeAnn, $allDateAccountArray)) {

									echo "<td align='center'>$tempAccount</td>";
									echo "<td align='center'>$tempDay</td>";
									echo "<td align='center'>$tempAccount</td>";
									echo "<td align='center'>$tempDay</td>";
									echo "<td align='center'>工作类型错误,假日不得写正常,请假或年假</td>";
								}
							}
						}
						else {
							
						}
					}
					?>
				</tr>
			</table>
			</br>
<?php if ($pager != null) { ?>
				<table width="80%" border="1" cellpadding="0" cellspacing="0">
					<thead><tr><td colspan='10'><?php $pager->show(); ?></td></tr></thead>
					<tr height="19">
						<td align="center" height="38" width="54" rowspan="2"><font size='3'>序号</font></td>
						<td align="center" width="90" rowspan="2"><font size='3'>日期</font></td>
						<td align="center" width="72" rowspan="2"><font size='3'>姓名</font></td>
						<td align="center" width="72" rowspan="2"><font size='3'>员工编号</font></td>
						<td align="center" width="216" colspan="6"><font size='3'>详情</font></td>
					</tr>
					<tr height="19">
						<td align="center"  ><font size='3'>工作内容</font></td>
						<td align="center" width="100" ><font size='3'>项目名称</font></td>
						<td align="center" width="72" ><font size='3'>类型</font></td>
						<td align="center" width="72" ><font size='3'>开始时间</font></td>
						<td align="center" width="72" ><font size='3'>结束时间</font></td>
						<td align="center"  width="72" ><font size='3'>小时</font></td>
					</tr>
					<?php
					$id = 0;
					foreach ($todos as $todo):$id+=1;
						?>
						<body link="blue" vlink="purple">
						<tr height="25">
							<td align="center"><?php echo $id; ?></td>
							<td align="center"><?php echo $todo->date; ?></td>
							<td align="center"><?php echo $this->kevincom->getRealnameByAccount($todo->account); ?></td>
							<td align="center"><?php echo $this->kevincom->getUserCodeByAccount($todo->account); ?></td>
							<td align="left"><?php echo html::a($this->createLink('todo', 'view', "id=$todo->id&from=my", '', true), $todo->name, '', "data-toggle='modal' data-type='iframe' data-title='" . $lang->kevinhours->view . "' data-icon='check'"); ?></td>
							<td align="center"><?php echo $this->kevincom->getProjectNameByProject($todo->project); ?></td>
							<td align="center" style="background-color:<?php echo $this->config->kevinhours->fontColor[$todo->hourstype]; ?>"><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
							<td align="center"><?php echo $todo->begin; ?></td>
							<td align="center"><?php echo $todo->end; ?></td>
							<td align="center"><?php echo $this->kevinhours->showHours($todo->minutes); ?></td>
						</tr>
	<?php endforeach; ?>
					</body>
					<tfoot><tr><td colspan='10'><?php $pager->show(); ?></td></tr></tfoot>
				</table>
<?php } ?>
	</form>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script type="text/javascript">
function printover()
{
	//打印个人的考勤时控件不存在
	var employeeObj = document.getElementById("userIndex");
	var employee = '';
	if (employeeObj != null)
		employee = employeeObj.value;

	var dept = '';
	var deptObj = document.getElementById("dept");
	if (deptObj != null)
		dept = deptObj.value;

	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	var season = document.getElementById("season").value;
	if ("" == month)
		month = "00";
	if ("" == season)
		season = "0";
	var fillauth = 0;
	if (document.getElementById('fillauth').checked)fillauth = 1;
	
	//window.location.href="my-kevinhours";
	window.open("kevinhours-printover-" + year + month + season + "-" + employee + "-" + dept + "-"+fillauth+".html");
}
</script>