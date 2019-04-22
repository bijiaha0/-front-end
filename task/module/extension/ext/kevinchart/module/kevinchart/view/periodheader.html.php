<?php
echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=toNewPeriod(this.value) class=form-control') . '</li>';
echo "<li><button class='btn form-control' onclick=\"changeMonth('-1');\"><i class='icon-backward'></i></button></li>";
echo '<li>' . html::select('month', $monthList, $currentMonth, 'onchange=toNewPeriod(this.value) class=form-control') . '</li>';
echo "<li><button class='btn form-control' onclick=\"changeMonth('+1');\"><i class='icon-forward'></i></button></li>";
echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+0');\">本月</button></li>";

echo "<li>&nbsp;</li>";
foreach ($lang->kevinchart->periods as $period => $label) {
	$vars	 = "period=$period";
	$vars	 = "id=$id&" . $vars;
	echo "<li id='$period'>" . html::a($this->createLink('kevinchart', $methodName, $vars), $label) . '</li>';
}
echo "<li id='byDate' class='datepicker-wrapper datepicker-date'>" . html::input('date', $currentDate, "class='form-control form-date' onchange='toNewPeriod(this.value)'") . '</li>';
?>
<li id='period'><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $lang->kevinchart->recently; ?></li>
<?php
foreach ($lang->kevinchart->endList as $period => $label) {
	$vars	 = "period=$period";
	$vars	 = "id=$id&" . $vars;
	echo "<li id='$period'>" . html::a($this->createLink('kevinchart', $methodName, $vars), $label) . '</li>';
}
?>
