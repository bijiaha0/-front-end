<?php js::import($jsRoot . 'kevin/kevin.js'); ?>
<div id='featurebar'>
	<ul class='nav'>
		<?php
		if (isset($yearList)):
			echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=toNewPeriodMonth() class=form-control') . '</li>';
			echo "<li><button class='btn form-control' onclick=\"toNewPeriod('-1');\"><i class='icon-backward'></i></button></li>";
			echo '<li>' . html::select('month', $monthList, $currentMonth, 'onchange=toNewPeriodMonth() class=form-control') . '</li>';
			echo "<li><button class='btn form-control' onclick=\"toNewPeriod('+1');\"><i class='icon-forward'></i></button></li>";

			echo "<li id='byDate' class='datepicker-wrapper datepicker-date'>" . html::input('date', $currentDate, "class='form-control form-date' onchange='toNewPeriodDate(this.value)'") . '</li>';

			echo "<li id='statusTab' class='dropdown'>";
			$status	 = isset($status) ? $status : '';
			$current = ''; //zget($lang->project->statusSelects, $status, '');
			//if(empty($current)) $current = $lang->project->statusSelects[''];
			echo html::a('javascript:;', $lang->kevinchart->recently . " <span class='caret'></span>", '', "data-toggle='dropdown'");
			echo "<ul class='dropdown-menu'>";
			foreach ($lang->kevinchart->periods as $timeIndex => $label) {
				$vars	 = "&period=$timeIndex";
				echo "<li id='$timeIndex'>" . html::a($this->createLink('kevinchart', $methodName, $vars), $label) . '</li>';
			}
			foreach ($lang->kevinchart->endList as $timeIndex => $label) {
				$vars	 = "&period=$timeIndex";
				echo "<li id='$timeIndex'>" . html::a($this->createLink('kevinchart', $methodName, $vars), $label) . '</li>';
			}
			echo '</ul></li>';
		endif
		?>
	</ul>
	<div class='actions'>
		<div class='btn-group'>

		</div>
	</div>
</div>
<?php
$headerHooks = glob(dirname(dirname(__FILE__)) . "/ext/view/featurebar.*.html.hook.php");
if (!empty($headerHooks)) {
	foreach ($headerHooks as $fileName)
		include($fileName);
}
?>
<script lanugage='javascript'>
	$('#<?php echo $methodName; ?>').addClass('active');

	var methodName = '<?php echo $methodName; ?>';
	var targetID = '<?php echo isset($id) ? $id : 0; ?>';
<?php if (isset($lastMonth)): ?>
		var lastMonth = '<?php echo $lastMonth; ?>';
		var nextMonth = '<?php echo $nextMonth; ?>';
<?php endif; ?>
</script>

