;function dispatched()
{
	//打印个人的考勤时控件不存在
	var employeeObj = document.getElementById("userIndex");
	var employee = '';
	if (employeeObj != null)
		employee = employeeObj.value;

	var dept = '';
	var deptObj = document.getElementById("dept");
	if (deptObj != null)
		dept = deptObj.value;

	var employeetype = '';
	var employeetypeObj = document.getElementById("employeetype");
	if (employeetypeObj != null)
		employeetype = employeetypeObj.value;

	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	var season = document.getElementById("season").value;
	if ("" == month)
		month = "00";
	if ("" == season)
		season = "0";

	var hourstype = document.getElementById("hourstype").value;

	var isIncludeAnn = 0;
	var tempObj = document.getElementById('isIncludeAnn1');
	if (document.getElementById('isIncludeAnn1').checked)
		isIncludeAnn = 1;
	window.open("kevinhours-dispatched-" + year + month + season + "-" + employee + "-" + dept + "-" + hourstype + "-" + employeetype + "-" + isIncludeAnn + ".html");
}
function printovertable()
{
	var dept = '';
	var deptObj = document.getElementById("dept");
	if (deptObj != null)
		dept = deptObj.value;

	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	var season = document.getElementById("season").value;
	if ("" == month)
		month = "00";
	if ("" == season)
		season = "0";

	var isIncludeAnn = 0;
	var tempObj = document.getElementById('isIncludeAnn1');
	if (document.getElementById('isIncludeAnn1').checked)
		isIncludeAnn = 1;
	window.open("kevinhours-printovertable-" + year + month + season + "-" + dept + "-" + isIncludeAnn + ".html");
}
function clearDateInput()
{
	document.getElementById('date').value = '';
}
function getEmployee(deptId)
{
	loadDeptEmployee(deptId);
}
function loadDeptEmployee(deptId)
{
	link = createLink('kevinhours', 'ajaxDeptEmployees', 'deptId=' + deptId);
	$('#userIdBox').load(link, function() {
		$('userIndex').chosen(defaultChosenOptions);
	});
}
function setCurrentMonth(initMonth)
{
	var month = document.getElementById('month').value;
	if ('' == month)
		document.getElementById('month').value = initMonth;
	document.getElementById('season').value = '';
}