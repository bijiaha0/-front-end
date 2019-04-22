<table>
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobileContent.":";?></th>
		<td><?php echo $todo->name;?>
		</td>
	</tr>
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobileProject.":";?></th>
		<td><?php echo $todo->project . ', ' . $this->kevincom->getProjectNameByProject($todo->project);?></td>
	</tr>
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobileDate.":";?></th>
		<td><?php
			$year=((int)substr($todo->date,0,4));//取得年份
			$month=((int)substr($todo->date,4,2));//取得月份
			$day=((int)substr($todo->date,6,2));//取得几号
			echo $year . '-' . $month . '-' . $day;
		?></td>
	</tr>
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobileBeginAndEnd.":";?></th>
		<td><?php echo $this->kevinhours->getHourAndMinutes($todo->begin) . ' ~ ' 
		. $this->kevinhours->getHourAndMinutes($todo->end);?></td>
	</tr>	
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobileTime.":";?></th>
		<td><?php echo $this->kevinhours->showHours($todo->minutes) . ' ' . $lang->kevinhours->hourunits;?></td>
	</tr>
	<tr>
		<th align='left'><?php echo $lang->todo->status.":";?></th>
		<td><?php echo $lang->kevinhours->statusList[$todo->status];?></td>
	</tr>
	<tr>
		<th align='left'><?php echo $lang->kevinhours->mobilehourstype.":";?></th>
		<td><?php echo $lang->kevinhours->hoursTypeList[$todo->hourstype];?></td>
	</tr>
</table>
<?php include '../../common/view/m.action.html.php';?>
