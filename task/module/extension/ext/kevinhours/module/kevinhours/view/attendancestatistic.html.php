		<tr height="30"><td colspan='41'>&nbsp;</td></tr>
		<tr height="15">
			<td align="center" colspan='41'><font size='3'><?php echo $company->name;echo $dateArray['year']?>年<?php echo $dateArray['month']?>月 -- 出勤表</font></td>
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
				if($days < $monthEnd)
				{
					for($i=$days+1; $i<=$monthEnd;$i++)
					{
						echo "<td align='center'><font size='2'>&nbsp;</font></td>";
					}
				}
			?>
			<td align="center" colspan='5'><font size='2'>出勤统计</font></td>
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
			<td align="center" rowspan="2"><font size='2'>正常</font></td>
			<td align="center" rowspan="2"><font size='2'>事假</font></td>
			<td align="center" rowspan="2"><font size='2'>年假</font></td>
			<td align="center" rowspan="2"><font size='2'>调休</font></td>
			<td align="center" rowspan="2"><font size='2'>实际<?php if('' != $isIncludeAnn) echo "(含年假)";?></font></td>
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
		</tr>
		<?php
			$id = 0;
			//遍历科室成员
			foreach($accountArray as $account)
			{
				if('' == $account) continue;
				$id += 1;
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
				
				$norHoursArray = array();
				$holHoursArray = array();
				$annHoursArray = array();
				$excHoursArray = array();
				//遍历考勤
				$todos=array();$j=0;
				foreach($allTodos as $todo):if($todo->account == $account)
				{
					$tempDateArray = array();
					$tempDateArray = explode("-",$todo->date);
					$tempDay = (int)$tempDateArray[2];//获得几号
					if(!in_array($tempDay, $normalDays)) continue;
					$hours = $this->kevinhours->showHours($todo->minutes);
					$todos[$tempDay][$j]=new stdClass();
					$todos[$tempDay][$j]=$todo;
					$j++;
					if('nor' == $todo->hourstype)
					{
						//正常
						if(array_key_exists($tempDay, $norHoursArray)) $norHoursArray[$tempDay] += $hours;
						else $norHoursArray[$tempDay] = $hours;
					}
					else if('hol' == $todo->hourstype)
					{
						//请假
						if(array_key_exists($tempDay, $holHoursArray)) $holHoursArray[$tempDay] += $hours;
						else $holHoursArray[$tempDay] = $hours;
					}
					else if('ann' == $todo->hourstype)
					{
						//年假
						if(array_key_exists($tempDay, $annHoursArray)) $annHoursArray[$tempDay] += $hours;
						else $annHoursArray[$tempDay] = $hours;
					}
					else if('rep' == $todo->hourstype)
					{
						//调休
						if(array_key_exists($tempDay, $excHoursArray)) $excHoursArray[$tempDay] += $hours;
						else $excHoursArray[$tempDay] = $hours;
					}
				}
				endforeach;
				//打印出勤情况
				$totalDay = 0;
				$totalHolDay = 0;
				$totalAnnDay = 0;
				$totalExcDay = 0;//调休
				for($tempIndex=1;$tempIndex<=31;$tempIndex++)
				{
					if($tempIndex <= $days)	$backgroundColor = (in_array($tempIndex, $normalDays)) ? '' : "style='background-color:#00ffff'";
					else $backgroundColor = '';
					if(array_key_exists($tempIndex, $norHoursArray))
					{
						if($norHoursArray[$tempIndex] == 8)
						{
							echo "<td align='center' $backgroundColor>Y</td>";
							$totalDay += 1;
						}
						else
						{
							$abnorflag=$this->kevinhours->checkabnormal($todos[$tempIndex],$tempIndex);
							if($abnorflag)
							{
								echo "<td align='center' $backgroundColor>异</td>";
							}
							else if(array_key_exists($tempIndex, $holHoursArray))
							{
								echo "<td align='center' $backgroundColor>Y/S</td>";
								$totalHolDay += 0.5;
							}
							else if(array_key_exists($tempIndex, $annHoursArray))
							{
								echo "<td align='center' $backgroundColor>Y/N</td>";
								$totalAnnDay += 0.5;
							}
							else if(array_key_exists($tempIndex, $excHoursArray))
							{
								echo "<td align='center' $backgroundColor>Y/T</td>";
								$totalExcDay += 0.5;
							}
							else echo "<td align='center' $backgroundColor>异</td>";//此处为异常,正常时间不足8小时
							//出勤时间算上年假
							if('' != $isIncludeAnn && array_key_exists($tempIndex, $annHoursArray)) $totalDay += 1;
							else $totalDay += 0.5;
						}
					}
					else
					{
						if(array_key_exists($tempIndex, $holHoursArray) && array_key_exists($tempIndex, $annHoursArray))
						{
							echo "<td align='center' $backgroundColor>S/N</td>";
							if('' != $isIncludeAnn) $totalDay += 0.5;
							$totalAnnDay += 0.5;
							$totalHolDay += 0.5;
						}
						else if(array_key_exists($tempIndex, $holHoursArray) && array_key_exists($tempIndex, $excHoursArray))
						{
							echo "<td align='center' $backgroundColor>S/T</td>";
							$totalExcDay += 0.5;
							$totalHolDay += 0.5;
						}
						else if(array_key_exists($tempIndex, $annHoursArray) && array_key_exists($tempIndex, $excHoursArray))
						{
							echo "<td align='center' $backgroundColor>T/N</td>";
							if('' != $isIncludeAnn) $totalDay += 0.5;
							$totalAnnDay += 0.5;
							$totalExcDay += 0.5;
						}
						else if(array_key_exists($tempIndex, $holHoursArray))
						{
							echo "<td align='center' $backgroundColor>S</td>";
							$totalHolDay += 1;
						}
						else if(array_key_exists($tempIndex, $excHoursArray))
						{
							echo "<td align='center' $backgroundColor>T</td>";
							$totalExcDay += 1;
						}
						else if(array_key_exists($tempIndex, $annHoursArray))
						{
							echo "<td align='center' $backgroundColor>N</td>";
							if('' != $isIncludeAnn) $totalDay += 1;
							$totalAnnDay += 1;
						}
						else echo "<td align='center' $backgroundColor>&nbsp;</td>";
					}
				}
				echo "<td align='center'>$actualDays</td>";
				echo "<td align='center'>$totalHolDay</td>";
				echo "<td align='center'>$totalAnnDay</td>";
				echo "<td align='center'>$totalExcDay</td>";
				echo "<td align='center'>$totalDay</td>";
				echo "</tr>";
			}
		?>
		<tr height="15">
			<td align="left" colspan='41'><font size='2'><?php echo "注：Y为出勤,N为年假,S为事假,T为调休,异为考勤异常.
																		例如：Y/N为请半天年假。其中考勤异常算半天出勤.
																		异常情况为正常工时不足八小时且无请假.";?></font></td>
		</tr>
	</table>
</body>
</div>