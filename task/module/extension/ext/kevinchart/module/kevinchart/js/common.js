function toNewPeriod(type)
{
	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;

	var currentDate = GetNewPeriodMonth(type,year,month);
	var link = createLink('kevinchart', methodName, 'period='+currentDate);
	location.href=link;
}
function toNewPeriodMonth( )
{
	var link;
	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	link = createLink('kevinchart', methodName, 'period='+year+month);
	location.href=link;
}
function toNewPeriodDate(date)
{
	date = date.replace(/\-/g, '');
	link = createLink('kevinchart', methodName,'period='+date);
	location.href=link;
}