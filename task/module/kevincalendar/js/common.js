function changeMonth(type)
{
	var currentDate;
	if ("+1" === type) {
		currentDate = nextMonth;
	}
	else if ("-1" === type) {
		currentDate = lastMonth;
	}
	else if ("+0" === type) {
		currentDate = thisMonth;
	}
	if ("log" === methodName) {
		var myDate = new Date();
		var currentDay = '' + myDate.getDate();
		if (currentDay.length <2) currentDay = '0'+currentDay;
		currentDate = currentDate + currentDay;
	}
	var link;
	link = createLink('kevincalendar', methodName, 'type=' + currentDate);

	location.href = link;
}
function getNewTodoList()
{
	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	if ('log' === methodName) {
		month = month + '01';
	}
	var link;
	link = createLink('kevincalendar', methodName, 'type=' + year + month);
	location.href = link;
}