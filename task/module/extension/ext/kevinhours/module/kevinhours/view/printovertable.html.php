<!DOCTYPE html>
<html lang='zh-cn'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title><?php echo '加班表/出勤表－打印';?></title>
</head>
<body>
<div class='container mw-1400px'>
<body link="blue" vlink="purple">
	<table width="1400" border="1" cellpadding="0" cellspacing="0">
		<tr height="15">
			<td align="center" colspan='41'><font size='3'><?php echo $company->name;echo $dateArray['year']?>年<?php echo $dateArray['month']?>月 -- 加班表</font></td>
		</tr>
		<tr height="10">
			<td align="center" rowspan="3" width='80'><font size='2'>部门</font></td>
			<td align="center" rowspan="3" width='30'><font size='2'>序号</font></td>
			<td align="center" rowspan="3" width='120'><font size='2'>科室</font></td>
			<td align="center" rowspan="3" width='50'><font size='2'>工号</font></td>
			<td align="center" width='60'><font size='2'>类型</font></td>
			<?php
				//获得记录的日历
				$legalHoliDays = array();
				$normalDays= array();
				$weekendArray= array();
				$tempDay  = '';
				foreach($calendarArray as $currentDate => $calendar)
				{
					$tempDay = explode('-',$currentDate)[2];
					if($lang->kevinhours->hoursTypeLaw == $calendar->status) $legalHoliDays[] = (int)$tempDay;
					else if($lang->kevinhours->hoursTypeHol == $calendar->status) $weekendArray[] = (int)$tempDay;
					else $normalDays[] = (int)$tempDay;
				}
				//获得一个月有几天
				$days = cal_days_in_month(CAL_GREGORIAN,$dateArray['month'],$dateArray['year']);
				$monthEnd = 31;
				for($i=1;$i<=$days;$i++)
				{
					$date = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $i;//日期
					$date = date('Y-m-d',strtotime($date));
					//是否存在于记录的日历中
					if(!array_key_exists($date, $calendarArray))
					{
						$unix=strtotime($date);//获得日期的 Unix 时间戳
						$week = date("w",$unix);//获得是周几,周末为0,周一为1
						if($week > 5 || $week < 1) $weekendArray[] = $i;
						else $normalDays[] = $i;
						
					}
					if(in_array($i, $normalDays)) echo "<td align='center'><font size='2'>W</font></td>";	
					else if(in_array($i, $legalHoliDays)) echo "<td align='center' style='background-color:#00ffff'><font size='2'>H</font></td>";
					else echo "<td align='center' style='background-color:#00ffff'><font size='2'>S</font></td>";
					
				}
				$actualDays = count($normalDays);
				if($days < $monthEnd)
				{
					for($i=$days+1; $i<=$monthEnd;$i++)
					{
						echo "<td align='center'><font size='2'>&nbsp;</font></td>";
					}
				}
			?>
			<td align="center"><font size='2'>W</font></td>
			<td align="center"><font size='2'>S</font></td>
			<td align="center"><font size='2'>H</font></td>
			<td align="center" colspan='2'><font size='2'>&nbsp;</font></td>
		</tr>
		<tr height="10">
			<td align="center" width="54"><font size='2'>日期</font></td>
			<?php 
				for($i=1;$i<=$days;$i++)
				{
					$backgroundColor = (in_array($i, $normalDays)) ? '' : "style='background-color:#00ffff'";
					echo "<td align='center' $backgroundColor><font size='2'>$i</font></td>";
				}
				if($days < $monthEnd)
				{
					for($i=$days+1; $i<=$monthEnd;$i++)
					{
						echo "<td align='center'><font size='2'>&nbsp;</font></td>";
					}
				}
			?>
			<td align="center" rowspan="2"><font size='2'>平时</font></td>
			<td align="center" rowspan="2"><font size='2'>周末</font></td>
			<td align="center" rowspan="2"><font size='2'>法定</font></td>
			<td align="center" colspan='2'><font size='2'>加班就餐</font></td>
		</tr>
		<tr height="10">
			<td align="center" width="54"><font size='2'>星期&姓名</font></td>
			<?php
				$weekarray=array('日','一','二','三','四','五','六');
				for($i=1;$i<=$days;$i++)
				{
					$date = $dateArray['year'] . '-' . $dateArray['month'] . '-' . $i;//日期
					$unix=strtotime($date);//获得日期的 Unix 时间戳
					$week = date("w",$unix);//获得是周几,周末为0,周一为1
					$backgroundColor = (in_array($i, $normalDays)) ? '' : "style='background-color:#00ffff'";
					echo "<td align='center' $backgroundColor><font size='3'>$weekarray[$week]</font></td>";
				}
				if($days < $monthEnd)
				{
					for($i=$days+1; $i<=$monthEnd;$i++)
					{
						echo "<td align='center'><font size='2'>&nbsp;</font></td>";
					}
				}
			?>
			<td align="center" colspan='2'><font size='3'>天</font></td>
		</tr>
		<?php
			//代办所有者数组
			$accountArray = array(); $lastAccount='';
			$accountNotInTodoArray = array();
			//遍历代办
			foreach($todos as $todo):
				//获得代办所有者数组
				if($lastAccount!=$todo->account)
				{
					$lastAccount=$todo->account;
					$currentDeptName = $this->kevincom->getDeptByAccount($todo->account);
					$currentgrade=$this->kevinhours->getdeptinfobyaccount($todo->account);
					$arrayIndex = $currentgrade.$currentDeptName .'-' . $todo->code . $todo->account;
					$accountArray[$arrayIndex]=$lastAccount;
				}
			endforeach;
			foreach($accountArrayInDept as $account)
			{
				$currentDeptName = $this->kevincom->getDeptByAccount($account);
				$currentAccountCode = $this->kevincom->getUserCodeByAccount($account);
				$currentgrade=$this->kevinhours->getdeptinfobyaccount($account);
				$arrayIndex = $currentgrade.$currentDeptName .'-' . $currentAccountCode . $account;
				if(!array_key_exists($arrayIndex, $accountArray))
				{
					$accountArray[$arrayIndex] = $account;
					$accountNotInTodoArray[] = $account;
				}
			}
			$accountsort=array();
			foreach($accountArray as $k=>$v){
				$k=iconv('UTF-8','GBK//IGNORE',$k);
				$accountsort[$k]=$v;
			}
			ksort($accountsort);
			$accountArray=array();
			foreach($accountsort as $k=>$v){
				$k=iconv('GBK','UTF-8//IGNORE',$v);
				$accountArray[$k]=$v;
			}
//			ksort($accountArray);
		?>
		<?php
			$id = 0;
			//遍历科室成员
			foreach($accountArray as $account)
			{
				if('' == $account) continue;
				$id += 1;
				if(in_array($account, $accountNotInTodoArray))
				{
					$realname = $this->kevincom->getRealnameByAccount($account);
					$code = $this->kevincom->getUserCodeByAccount($account);
					$deptParentName = ('' == $code) ? $this->kevincom->getDeptByAccount($account) : $this->kevincom->getSectionNameByAccount($account);
					$deptName = ('' == $code) ? $deptName : $this->kevincom->getDeptByAccount($account);
					echo "<td align='center'><font size='1'>$deptParentName</font></td>";
					echo "<td align='center'>$id</td>";
					echo "<td align='center'><font size='1'>$deptName</font></td>";
					if('' == $code) $code = "&nbsp;";
					echo "<td align='center'>$code</td>";
					echo "<td align='center'><font size='2'>$realname</font></td>";
					for($tempIndex=1;$tempIndex<=34;$tempIndex++)
					{
						if($tempIndex <= $days)	$backgroundColor = (in_array($tempIndex, $normalDays)) ? '' : "style='background-color:#00ffff'";
						else $backgroundColor = '';
						echo "<td align='center' $backgroundColor>&nbsp;</td>";
					}
					echo "<td  colspan='2'>&nbsp;</td>";
					echo "</tr>";
					continue;
				}
				$holidayOveHours = 0;
				$weekdayOveHours = 0;
				$weekendOveHours = 0;
				$firstDay = 1;//从1号开始遍历
				$oveEatingTimes = 0;
				$oveHoursArray = array();
				$realname = $this->kevincom->getRealnameByAccount($account);
				$code = $this->kevincom->getUserCodeByAccount($account);

				$deptParentName = ('' == $code) ? $this->kevincom->getDeptByAccount($account) : $this->kevincom->getSectionNameByAccount($account);
				$deptName = ('' == $code) ? $deptName : $this->kevincom->getDeptByAccount($account);
				echo "<tr height='19'>";
				echo "<td align='center'><font size='1'>$deptParentName</font></td>";
				echo "<td align='center'>$id</td>";
				echo "<td align='center'><font size='1'>$deptName</font></td>";
				echo "<td align='center'>$code</td>";
				echo "<td align='center'><font size='2'>$realname</font></td>";
				//遍历考勤
				foreach($todos as $todo):if($todo->account == $account)
				{
					$tempDateArray = array();
					$tempDateArray = explode("-",$todo->date);
					$tempDay = (int)$tempDateArray[2];//获得几号
					$hours = $this->kevinhours->showHours($todo->minutes);
					//获得加班日期数组对应的当天加班时间
					if(array_key_exists($tempDay, $oveHoursArray)) $oveHoursArray[$tempDay] += $hours;
					else $oveHoursArray[$tempDay] = $hours;
					//计算平时加班和周末加班时间
					if(in_array($tempDay, $weekendArray)) $weekendOveHours += $hours;
					else if(in_array($tempDay, $legalHoliDays)) $holidayOveHours += $hours;
					else $weekdayOveHours += $hours;
				}
				endforeach;
				//打印加班时间
				for($j=$firstDay;$j<=$monthEnd;$j++)
				{
					$backgroundColor = (in_array($j, $normalDays)) ? '' : "style='background-color:#00ffff'";
					if(array_key_exists($j, $oveHoursArray))
					{
						//打印
						$hoursToShow = $oveHoursArray[$j];
						if($hoursToShow >= 4) $oveEatingTimes+=1;
						echo "<td align='center' $backgroundColor>$hoursToShow</td>";

					}
					else
					{
						echo "<td align='center' $backgroundColor>&nbsp;</td>";
					}
				}
				//平时
				echo "<td align='center'>$weekdayOveHours</td>";
				//周末
				echo "<td align='center'>$weekendOveHours</td>";
				//法定
				echo "<td align='center'>$holidayOveHours</td>";
				//加班就餐
				if($oveEatingTimes == 0) echo "<td align='center' colspan='2'>&nbsp;</td>";
				else echo "<td align='center' colspan='2'>$oveEatingTimes</td>";
				echo "</tr>";
			}
		?>
<?php include "./attendancestatistic.html.php";?>
</body>
</html>