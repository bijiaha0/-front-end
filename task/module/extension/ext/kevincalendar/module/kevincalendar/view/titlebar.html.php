<?php
if (!isonlybody()) {
	$linkCom = $type?"type=$type":'';
	echo "<div id='featurebar'>";
	echo "<ul class='nav'>";
	echo "<li id='index'>" . html::a(helper::createLink('kevincalendar', 'index',$linkCom), "" . $lang->kevincalendar->calendar, '', "'") . '</li>';
	if (common::hasPriv('kevincalendar', 'log')) {
		echo "<li id='log'>" . html::a(helper::createLink('kevincalendar', 'log',$linkCom), "" . $lang->kevincalendar->log, '', "") . '</li>';
	}
	if (common::hasPriv('kevincalendar', 'todo')) {
		echo "<li id='todo'>" . html::a(helper::createLink('kevincalendar', 'todo',$linkCom), "" . $lang->kevincalendar->todo, '', "") . '</li>';
	}
	if (common::hasPriv('kevincalendar', 'lists')) {
		echo "<li id='lists'>" . html::a(helper::createLink('kevincalendar', 'lists',$linkCom), "". $lang->kevincalendar->lists, '', "") . '</li>';
	}
	if ('create' !== $controlType && 'batchcreate' !== $controlType) {
		echo '<li>' . html::select('year', $yearList, $currentYear, 'onchange=getNewTodoList() class=form-control') . '</li>';
		echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('-1');\"><i class='icon-backward'></i></button></li>";
		echo '<li>' . html::select('month', $monthList, $currentMonth, 'onchange=getNewTodoList() class=form-control') . '</li>';
		echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+1');\"><i class='icon-forward'></i></button></li>";
		echo "<li><button class='btn btn-default form-control' onclick=\"changeMonth('+0');\">本月</button></li>";
	}
	if ('create' === $controlType || 'batchcreate' === $controlType) {
		echo "<script>$('#index').addClass('active');</script>";
	} else {
		echo "<script>$('#$controlType') . addClass('active');</script>";
	}

	echo '</ul>';
}

echo "<div class='actions'>";
if ('lists' === $controlType || 'index' === $controlType) {
	echo html::a(helper::createLink('kevincalendar', 'batchcreate'), "<i class='icon-plus-sign'></i> " . $lang->kevincalendar->batchcreate, '', "class='btn'");
	echo html::a(helper::createLink('kevincalendar', 'create'), "<i class='icon-plus'></i> " . $lang->kevincalendar->create, '', "class='btn'");
}
echo "</div></div>";
?>
<script>
var nextMonth = '<?php if (isset($nextMonth)) echo $nextMonth; ?>';
var lastMonth = '<?php if (isset($lastMonth)) echo $lastMonth; ?>';
var thisMonth = '<?php if (isset($thisMonth)) echo $thisMonth; ?>';
var methodName = '<?php if (isset($controlType)) echo $controlType; ?>';
</script>