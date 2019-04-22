<!DOCTYPE html>
<?php
$ShowProjectName = isset($this->config->kevinhours->dispatchedShowProjectName)?
    $this->config->kevinhours->dispatchedShowProjectName:false;
?>
<html lang='zh-cn'>
  <head>
	<meta charset='utf-8'>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<title><?php echo '外协工作记录表－打印'; ?></title>
  </head>
  <body>
	<div class='container mw-1400px'>
	  <form method='post' id='todoform'>
		<center>
			<?php
			//代办所有者数组
			$accountArray	 = array();
			$lastAccount	 = '';
			//遍历代办
			foreach ($todos as $todo):
				//获得代办所有者数组
				if ($lastAccount != $todo->account) {
					$lastAccount	 = $todo->account;
					$accountArray[]	 = $lastAccount;
				}
			endforeach;
			$this->loadModel('kevincom');
			?>
			<?php
			$length = count($accountArray);
			for ($i = 0; $i < $length; $i++) {
				?>
			  <body link="blue" vlink="purple">
				<table width="800" border="0" cellpadding="0" cellspacing="0">
				  <col width="54" />
				  <col width="72"/>
				  <col width="135"/>
				  <col width="96"/>
				  <col width="95"/>
				  <col width="72"/>
				  <tr>
					<th colspan="8" align="center"><font size='6'>外协工作记录</font></th>
				  </tr>
				  <tr height="40">
					<td colspan="5" align="right"><font size='3'>外协设计流程流程编号:&nbsp;</font></td>
					<td colspan="3" align="left"><font size='3'><u><?php
					  for ($j = 0; $j < 25; $j++) {
						  echo "&nbsp;";
					  }
					  ?></u></font></td>
				  </tr>
				</table>
				<table width="800" border="1" cellpadding="0" cellspacing="0">
				  <tr height="35">
					<th colspan="1" align="center"><font size='4'>公司</font></th>
					<td colspan="2" align="center"><font size='4'>&nbsp;<?php echo $this->kevincom->getDeptNameByAccount($accountArray[$i]); ?></font></td>
					<th colspan="1" align="center"><font size='4'>姓名</font></th>
					<td colspan="1" align="center"><font size='4'><?php echo $this->kevincom->getRealnameByAccount($accountArray[$i]); ?></font></td>
					<th colspan="2" align="center"><font size='4'>手机</font></th>
					<td colspan="2" align="center"><font size='4'><?php echo $this->kevincom->getUserInfoByAccount($accountArray[$i])->mobile . '&nbsp;'; ?></font></td>
				  </tr>
				  <tr height="30">
					<th colspan="1" align="center"><font size='3' id='cardid'><strong>卡号</strong></font></th>
					<td colspan="2" align="center"><font size='4'>&nbsp;<?php echo $this->kevincom->getUserInfoByAccount($accountArray[$i])->cardId; ?></font></td>
					<td colspan="8" align="center"><font size='3'><strong>每日工作记录</strong></font></td>
				  </tr>
				  <tr height="19">
					<th align="center" width="140"><font size='3'><?php echo $lang->kevinhours->date; ?></font></th>
					<th align="center" width="200"><font size='3'><?php echo $lang->kevinhours->timeunits; ?></font></th>
					<th align="center" width="90"><font size='3'><?php echo $lang->kevinhours->type; ?></font></th>
					<th align="center" width="100"><font size='3'><?php echo $lang->kevinhours->beginAB; ?></font></th>
					<th align="center" width="100"><font size='3'><?php echo $lang->kevinhours->endAB; ?></font></th>
					<th align="center" width="150"><font size='3'><?php echo $lang->kevinhours->projectId; ?></font></th>
					<th align="center" width="170"><font size='3'><?php if($ShowProjectName)echo $lang->kevinhours->projectName;
                        else echo $lang->kevinhours->cashCode; ?></font></th>
					<th align="center" width="550"><font size='3'><?php echo $lang->kevinhours->workcontenttitle; ?></font></th>
				  </tr>
				  <?php
				  //代办项目代号数组
				  $cashCodeArray	 = array();
				  //代办项目人时数组
				  $manHoursArray	 = array();
				  //定义正常加班和请假工作时间总计
				  $norHours		 = 0; //正常
				  $oveHours		 = 0; //加班
				  $holHours		 = 0; //请假
				  $repHours		 = 0; //调休
				  $annHours		 = 0; //年假
				  $currentHours	 = 0;
				  foreach ($todos as $todo):if ($todo->account == $accountArray[$i]) {
						  $currentHours	 = $this->kevinhours->showHours($todo->minutes);
						  $cashCode		 = $this->kevincom->getCashCodeByProject($todo->project);
						  //获得代办中项目代号数组
						  if (!in_array($cashCode, $cashCodeArray)) {
							  $cashCodeArray[] = $cashCode;
						  }
						  //获得项目对应的人时
						  if ($lang->kevinhours->hoursTypeHol == $todo->hourstype) {
							  $holHours+=$currentHours;
						  } else if ($lang->kevinhours->hoursTypeAnn == $todo->hourstype) {
							  $annHours+=$currentHours;
						  } else if ($lang->kevinhours->hoursTypeRep == $todo->hourstype) {
							  $repHours+=$currentHours;
						  } else {
							  if (array_key_exists($cashCode, $manHoursArray))
								  $manHoursArray[$cashCode] += $currentHours;
							  else
								  $manHoursArray[$cashCode] = $currentHours;
							  if ($lang->kevinhours->hoursTypeNor == $todo->hourstype)
								  $norHours+=$currentHours;
							  else if ($lang->kevinhours->hoursTypeOve == $todo->hourstype)
								  $oveHours+=$currentHours;
						  }
						  ?>
						  <tr height="19">
							<td align="center"><?php echo $todo->date; ?></td>
							<td align="center"><?php echo $currentHours; ?></td>
							<td align="center"><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype]; ?></td>
							<td align="center"><?php
								if ('' != $todo->begin)
									echo $todo->begin;
								else
									echo "&nbsp";
								?></td>
							<td align="center"><?php
								if ('' != $todo->end)
									echo $todo->end;
								else
									echo "&nbsp";
								?></td>
							<td align="right"><?php echo $todo->project;?></td>
							<td align="right"><?php 
                            if($ShowProjectName)  echo  $todo->projectname;
                            else echo $todo->cashCode?$todo->cashCode:'&nbsp';?></td>
							<td align="left"><?php echo $todo->name; ?></td>
						  </tr>
						  <?php
					  } endforeach;
				  //项目代号数组排序,按数字大小
				  sort($cashCodeArray, SORT_NUMERIC);
				  ?>
				</table>
				<table width="800" border="1" cellpadding="0" cellspacing="0">
				  <tr height="30"><th colspan=<?php echo count($cashCodeArray) + 1; ?> align="center"><font size='3'>项目人时总计(人时总计不含请假)</font></th></tr>
				  <tr>
					<th align="center"><font size='3'><?php echo $lang->kevinhours->cashCode; ?></font></th>
					<?php
					$cashCodeArrayLength = count($cashCodeArray);
					$isShowHoursShare	 = false;
					for ($cashCodeIndex = 0; $cashCodeIndex < $cashCodeArrayLength; $cashCodeIndex++) {
						$tempCashCode = $cashCodeArray[$cashCodeIndex];
						if ('' == $tempCashCode) {
							$isShowHoursShare	 = true;
							$tempCashCode		 = '无';
						}
						echo "<th align='center'><font size='3'>$tempCashCode</font></th>";
					}
					?>
				  </tr>
				  <tr>
					<th align="center"><font size='3'>人时</font></th>
					<?php
					$totalHours = 0;
					for ($cashCodeIndex = 0; $cashCodeIndex < $cashCodeArrayLength; $cashCodeIndex++) {
						$project = $cashCodeArray[$cashCodeIndex];
						$hours	 = 0;
						if (array_key_exists($project, $manHoursArray)) {
							$hours = $manHoursArray[$project];
						}
						//将要分摊的时间加上年假
						if ('' == $project && '' != $isIncludeAnn) {
							$hours += $annHours;
						}
						$totalHours += $hours;

						echo "<th align='center'><font size='3'>$hours</font></th>";
					}
					?>

				  </tr>
				  <?php if ($isShowHoursShare) { ?>
					  <tr>
						<th align="center"><font size='3'>工时分摊</font></th>
						<?php
						$perHours		 = 0; //每小时要分摊的工时
						$totalAddHours	 = 0; //总计要分摊的工时
						$sharedHours	 = 0; //已分摊的工时
						for ($cashCodeIndex = 0; $cashCodeIndex < $cashCodeArrayLength; $cashCodeIndex++) {
							//获得要均摊的工时
							if ($cashCodeArray[$cashCodeIndex] == '' && $cashCodeArrayLength > 1) {
								$otherTotalHours = 0.0;
								//获得除要均摊的工时外的工时总和
								foreach ($manHoursArray as $tempKey => $tempValue) {
									if ('' == $tempKey)
										continue;
									$otherTotalHours += $tempValue;
								}
								$totalAddHours = 0.0;
								if (array_key_exists('', $manHoursArray)) {
									$totalAddHours = $manHoursArray[''];
								}
								//将要分摊的时间加上年假
								if ('' != $isIncludeAnn)
									$totalAddHours += $annHours;
								$perHours = $totalAddHours / $otherTotalHours;

								echo "<th align='center'><font size='3'>&nbsp;</font></th>";
								continue;
							}
							if ($cashCodeIndex == $cashCodeArrayLength - 1) {
								$project	 = $cashCodeArray[$cashCodeIndex];
								$hours		 = empty($project) ? 0 : $manHoursArray[$project];
								$addHours	 = $totalAddHours - $sharedHours;
								$hours += $addHours;
								echo "<th align='center'><font size='3'>$hours</font></th>";
								continue;
							}
							$project		 = $cashCodeArray[$cashCodeIndex];
							$hours			 = empty($project) ? 0 : $manHoursArray[$project];
							//均摊工时保留小数点一位并四舍五入
							$tempSharedHours = number_format($perHours * $hours, 1);
							$sharedHours += $tempSharedHours; //累加已分摊工时
							$hours += $tempSharedHours; //当前付费号累加均摊工时后的时间
							echo "<th align='center'><font size='3'>$hours</font></th>";
						}
						?>
					  </tr>
				  <?php } ?>
				</table>
				<table width="800" border="1" cellpadding="0" cellspacing="0">
				  <!-- 此处控制各项列宽-->
				  <col width="150"/>
				  <col width="100" />
				  <col width="100"/>
				  <col width="100"/>
				  <col width="100"/>
				  <col width="100"/>
				  <tr height="35">
					  <?php
					  if ('' != $isIncludeAnn)
						  echo "<th rowspan='2' align='center'><font size='4'>总计(含年假)</font></th>";
					  else
						  echo "<th rowspan='2' align='center'><font size='4'>总计(不含年假)</font></th>";
					  ?>
					<th rowspan='2' align="center"><font size='4'>正常</font></th>
					<th rowspan='2' align="center"><font size='4'>加班</font></th>
					<th colspan="3" align="center"><font size='4'>请假</font></th>
				  </tr>
				  <tr>
					<th colspan="1" align="center"><font size='4'>请假</font></th>
					<th colspan="1" align="center"><font size='4'>年假</font></th>
					<th colspan="1" align="center"><font size='4'>调休</font></th>
				  </tr>
				  <tr height="35">
					<td colspan="1" align="center"><font size='4'><?php echo $totalHours; ?></font></td>
					<td colspan="1" align="center"><font size='4'><?php echo $norHours; ?></font></td>
					<td colspan="1" align="center"><font size='4'><?php echo $oveHours; ?></font></td>
					<td colspan="1" align="center"><font size='4'><?php echo $holHours; ?></font></td>
					<td colspan="1" align="center"><font size='4'><?php echo $annHours; ?></font></td>
					<td colspan="1" align="center"><font size='4'><?php echo $repHours; ?></font></td>
				  </tr>
				</table>
				<table width="800" border="1" cellpadding="0" cellspacing="0">
				  <col width="60" />
				  <col width="100"/>
				  <col width="60"/>
				  <col width="100"/>
				  <tr height="40">
					<th colspan="1" align="center"><font size='4'>审核</font></th>
					<td colspan="1" align="center"><font size='4'>&nbsp;</font></td>
					<th colspan="1" align="center"><font size='4'>日期</font></th>
					<td colspan="1" align="center"><font size='4'>&nbsp;</font></td>
				  </tr>
				</table>
			  </body>
			  <?php if ($length == 1) continue; ?>
			  <p class=normal style="PAGE-BREAK-BEFORE:always"></p>
		  <?php } ?> 
	  </form>
	</div>
  </body>
</html>