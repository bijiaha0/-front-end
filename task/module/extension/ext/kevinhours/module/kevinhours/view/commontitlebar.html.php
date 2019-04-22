<?php
unset($monthList['']);
echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=getNewTodoList() class=form-control') . '</li>';
echo "<li><button class='btn form-control' onclick=\"changeMonth('-1');\"><i class='icon-backward'></i></button></li>";
echo '<li>' . html::select('month', $monthList, $currentMonth, 'onchange=getNewTodoList() class=form-control') . '</li>';
echo "<li><button class='btn form-control' onclick=\"changeMonth('+1');\"><i class='icon-forward'></i></button></li>";
echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+0');\">本月</button></li>";
echo "<li>&nbsp;</li>";
if (empty($disablePeriods)) {
	foreach ($lang->kevinhours->periods as $period => $label) {
		$vars	 = "type=$period";
		if ('project' === $methodName)
			$vars	 = "projectID=$projectID&" . $vars;
		else if ('product' === $methodName)
			$vars	 = "productID=$productID&" . $vars;
		elseif($methodName=='my'||$methodName=='todo')
			$vars.="&account={$this->kevinhours->account}&deptID={$this->kevinhours->accountdept}";
		echo "<li id='$period'>" . html::a($this->createLink('kevinhours', $methodName, $vars), $label) . '</li>';
	}
	echo "<li id='byDate' class='datepicker-wrapper datepicker-date'>" . html::input('date', $currentDate, "class='form-control form-date' onchange='changeDate(this.value)'") . '</li>';
}
if (empty($disableRecently)) {
	?>
	<li id='period'><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $lang->kevinhours->recently; ?></li>
	  <?php
	  foreach ($lang->kevinhours->endList as $period => $label) {
		  $vars	 = "type=$period";
		  if ('project' === $methodName)
			  $vars	 = "projectID=$projectID&" . $vars;
		  else if ('product' === $methodName)
			  $vars	 = "productID=$productID&" . $vars;
		  elseif($methodName=='my'||$methodName=='todo')
				$vars.="&account={$this->kevinhours->account}&deptID={$this->kevinhours->accountdept}";
		  echo "<li id='$period'>" . html::a($this->createLink('kevinhours', $methodName, $vars), $label) . '</li>';
	  }
  }
  ?>
<script>$('#<?php echo $type; ?>').addClass('active')</script>