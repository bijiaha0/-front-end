<table>
  <tr>
	<td> 
	  <div class='actions'><font size="3" color="#F00078" class="text-left">
		<?php
		//获得上月考勤修改的截止时间为本月的某天某时
		$lastMonthLock	 = $this->kevinhours->getLockedDateOfLastMonth();
		$thisMonthLock	 = $this->kevinhours->getLockedDateOfThisMonth();
		$tempStr		 = str_replace('%lastMonthLock%', date('m-d', $lastMonthLock), $this->lang->kevinhours->warning);
		$tempStr		 = str_replace('%lastMonth%', kevin::getMonth(-1), $tempStr);
		$tempStr		 = str_replace('%time%', $this->config->kevinhours->endTime, $tempStr);
		$tempStr		 = str_replace('%thisMonth%', (int) date('m'), $tempStr);
		;
		$tempStr		 = str_replace('%thisMonthLock%', date('m-d', $thisMonthLock), $tempStr);
		if (0 != $this->config->kevinhours->limitDate) {
			$tempStr .= str_replace('%4', $this->config->kevinhours->limitDate, $this->lang->kevinhours->beyondNotes);
		}
		echo $tempStr;
		if ('' == stristr($_SERVER["HTTP_USER_AGENT"], "firefox")) {
			echo $this->lang->kevinhours->tips;
		}
		?></font>
		<div><h4 class="mg-0"><?php echo $lang->kevinhours->suggest;?></h4></div>
		</div>
	</td>
	</tr>
</table>
