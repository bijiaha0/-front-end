<div id='featurebar'>
	<ul class='nav'>
		<li><i class="icon-user">&nbsp;<?php echo $this->kevinhours->employeesAll[$this->kevinhours->account]->realname;?> </i><i class="icon-angle-right"></i></li>
		<?php
		if ('index' == $controlType) {
			unset($monthList['']);
			echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=getNewTodoList() class=form-control') . '</li>';
			echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('-1');\"><i class='icon-backward'></i></button></li>";
			echo '<li>' . html::select('month', $monthList, $currentMonth, 'onchange=getNewTodoList() class=form-control') . '</li>';
			echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+1');\"><i class='icon-forward'></i></button></li>";
			echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+0');\">本月</button></li>";
		}
		?>
	</ul>
	<div class='actions'>
		<?php
		if (common::hasPriv('kevinhours', 'searchproject')) {
			echo html::a(helper::createLink('kevinhours', 'searchproject', '', '', true), $lang->kevinhours->searchproject, '', "class='btn export'");
		}
		if (common::hasPriv('kevinhours', 'finish')) {
			echo html::a(helper::createLink('kevinhours', 'finish', 'todoID=thisMonth', '', true), $lang->kevinhours->finishThisMonth, '', "class='btn btn-primary' data-toggle='modal' data-type='iframe' title={$lang->kevinhours->finishThisMonthTip}");
		}
		?>
	</div>
</div>
<script language='Javascript'>
	var currentAccount = '<?php echo $this->kevinhours->account; ?>';
	var currentdept='<?php echo $this->kevinhours->accountdept;?>';
	var nextMonth = '<?php echo $nextMonth; ?>';
	var lastMonth = '<?php echo $lastMonth; ?>';
	var thisMonth = '<?php echo $thisMonth; ?>';
	var methodName = '<?php echo $controlType; ?>';
</script>