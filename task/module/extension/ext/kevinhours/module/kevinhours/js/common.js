$('#wrap').css('padding-left','0px');
$('#wrap').css('padding-right','0px');
function changeDate(module,method,date)
{
	date = date.replace(/\-/g, '');
	link = createLink(module, method, 'date=' + date);
	location.href=link;
}
function setMonthInputNull()
{
	document.getElementById('month').value = '';
}
function setSeasonInputNull()
{
	document.getElementById('season').value = '';
}
function printservicelist()
{
	//Get param
	var deptObj = document.getElementById("dept");
	var dept = '';
	if(deptObj != null) dept = deptObj.value;
	
	var year = '';
	var yearObj = document.getElementById("year");
	if(yearObj != null) year = yearObj.value;
	
	var monthObj = document.getElementById("month");
	var month = '';
	if(monthObj != null) month = monthObj.value;
	
	var season = '';
	var seasonObj = document.getElementById("season");
	if(seasonObj != null) season = seasonObj.value;
	
	var classdept = '';
	var classdeptObj = document.getElementById("classdept");
	if(classdeptObj != null) classdept = classdeptObj.value;

	window.open(createLink('kevinhours', 'printservicelist', 'dept='+dept+'&year='+year+"&month="+month+"&season="+season+"&classdept="+classdept, '', true));
}
function gotoUpdateCashStat()
{
	//获得参数值
	var deptObj = document.getElementById("dept");
	var dept = '';
	if(deptObj != null) dept = deptObj.value;
	
	var year = '';
	var yearObj = document.getElementById("year");
	if(yearObj != null) year = yearObj.value;
	
	var monthObj = document.getElementById("month");
	var month = '';
	if(monthObj != null) month = monthObj.value;
	
	var season = '';
	var seasonObj = document.getElementById("season");
	if(seasonObj != null) season = seasonObj.value;

	window.location.href = createLink('kevinhours', 'updateCashStat', 'dept='+dept+'&year='+year+"&month="+month+"&season="+season);
}
function switchDateTodo(switcher)
{
    if(switcher.checked)
    {
        $('#date').attr('disabled','disabled');
    }
    else
    {
        $('#date').removeAttr('disabled');
    }
}

function loadList(type, id)
{
    if(id)
    {
        divClass = '.nameBox' + id;
        divID    = '#nameBox' + id;
    }
    else
    {
        divClass   = '.nameBox';
        divID      = '#nameBox';
    }

    var param = 'account=' + account;
    if(id) param += '&id=' + id;
    if(type == 'bug')
    {
        link = createLink('bug', 'ajaxGetUserBugs', param);
    }
    else if(type == 'task')
    {
        link = createLink('task', 'ajaxGetUserTasks', param);
    }

    if(type == 'bug' || type == 'task')
    {
        $(divClass).load(link, function(){$(divClass).find('select').chosen(defaultChosenOptions)});
    }
    else if(type == 'custom')
    {
        $(divClass).html($(divID).html());
    }
}

function switchDateFeature(switcher)
{
    if(switcher.checked) 
    {
        $('#begin').attr('disabled','disabled');
        $('#end').attr('disabled','disabled');
    }
    else
    {
        $('#begin').removeAttr('disabled');
        $('#end').removeAttr('disabled');
    }
}
function getObjectOfTime(i)
{
	var objBefore;
	var objEnd;
	var objMinutes;
	if(typeof i == 'undefined')
	{
		objBefore = document.getElementById('begin');
		objEnd = document.getElementById('end');
		objMinutes =  document.getElementById('minutes');
	}
	else
	{
		objBefore = document.getElementById('begins' + i);
		objEnd = document.getElementById('ends' + i);
		objMinutes =  document.getElementById('minutes[' + i + ']');
	}
	return [objBefore,objEnd,objMinutes]
}
function getProjectNameInBatchCreate(project, id)
{
	loadProjectNameByProjectInBatchCreate(project, id);
}
function loadProjectNameByProjectInBatchCreate(project, id)
{
	link = createLink('todo', 'ajaxProjectNameByProject', 'project=' + project);
	$('#projectNameBox'+id).load(link, function(){$('projectName' + id).chosen(defaultChosenOptions);});
}
function getProjectName(project)
{
	loadProjectNameByProject(project);
}
function loadProjectNameByProject(project)
{
	link = createLink('todo', 'ajaxProjectNameByProject', 'project=' + project);
	$('#projectNameBox').load(link, function(){$('projectName').chosen(defaultChosenOptions);});
}
/*
	自动修正 工时,例如
	输入1到24，认为是小时：9 9#等都返回9:00，
	输入>=25，后两位是分钟，前面是小时
	输入9.50,9。5 , 返回 9:30
	输入.5,。5，返回0:30
	输入9R30 9:30,9 30,9<字符>30，都返回9:30
	输入9：70，返回 10：10
	输入930，返回 9:30
	Kevin Yang 2014-10-31
*/
function modifyTimeInput(iTime)
{
	var len = iTime.length;
	if(0 == len)return "0:00";

	var hour = "";
	var minute = "";
	var isHour = true;
	var isNum = false;
	var isPoint = false; //is point like 1.5,1。5
	var isfindNum = false; //find num
	var isfindMinitue = false;//find minute
	
	var charNow = "";
	for(j = 0; j < len; j++)
	{
		charNow = iTime.charAt(j);
		isNum = charNow.match(/^\d.*$/);
		if (isNum)
		{
			isfindNum = true;
			if(isHour)
			{
				hour += charNow;
			}
			else
			{
				isfindMinitue = true;
				minute += charNow;
			}
		}
		else
		{
			if (!isPoint )
			{
				if('.' == charNow || '。' == charNow)
				{
					isPoint = true;
					if(!isfindNum)
					{
						isHour = false;	
					}
				}
			}
			if(isfindNum)
			{
				isHour = false;
			}
		}
	}
	minute = parseInt(minute/1);

	if(isPoint)
	{
		minute = '0.' + minute;
		minute = parseInt(60 * minute);
	}
	hour = parseInt(hour/1);

	//if only input hours ,and more than 24, we think it is only for minues
	if(!isfindMinitue)
	{
		if(hour>24)
		{
			minute = hour %100;
			hour = parseInt(hour/100);
			//minute = hour%60;
			//hour = parseInt(hour/60);
		}
	}
	
	if (minute>=60)
	{
		hour = hour + parseInt(minute/60);
		minute = minute%60;
	}
	
	if(hour>24)
	{
		hour = hour%24;
	}
	if(0 == minute)
	{
		minute = '00';	
	}
	else if(minute<10)
	{
		minute = '0' + minute;
	}

	return hour+ ":" + minute;
	
}

function onChaneTimeFinished(i)
{
	var startObj = $("#begins" + i)[0];
	var EndObj = $("#ends" + i)[0];
	var MinutesObj = $("#minues" + i)[0];

	if(!isHoliday)
	{//checked lunch time
		var isChange=false;
		if(startObj.value<todoWorkStart)
		{
			if(EndObj.value >todoWorkStart)
			{
				EndObj.value = todoWorkStart;
				isChange=true;
			}
		}
		if(startObj.value<todoEatingStart)
		{
			if(EndObj.value >todoEatingStart)
			{
				EndObj.value = todoEatingStart;
				isChange=true;
			}
		}	
		else if(startObj.value<todoEatingEnd)
		{
			startObj.value = todoEatingEnd;
			onChangeBefore(i);
			if(EndObj.value >todoWorkEnd) 
			{
				EndObj.value = todoWorkEnd;
			}
			isChange=true;
		}
		else if(startObj.value <todoWorkEnd)
		{
			if(EndObj.value >todoWorkEnd) 
			{
				EndObj.value = todoWorkEnd;
				isChange=true;
			}
		}
		else if(EndObj.value >todoWorkStart && EndObj.value <startObj.value)
		{
			EndObj.value = todoWorkStart;
			isChange=true;
		}
		if(isChange)SetMinutesValue(i);
	}
	
	if (startObj.value == EndObj.value)
	{
		MinutesObj.value = "1:00";
		onChangeMinutes(i);		
	}

	var timeValue = startObj.value;
	var Objhourstypes = document.getElementById('hourstypes' + i);
	if(isHoliday || timeValue >= todoWorkEnd || timeValue < todoWorkStart)
	{
		Objhourstypes.value = 'ove';
	}
	else
	{
		if(Objhourstypes.value != 'hol')Objhourstypes.value = 'nor';
	}
	SethourstypesColor(i);
}
//calculate minutes
function OnChangeHourstypes(i)
{
	SethourstypesColor(i);
}
function onChangeMinutes(i)
{
	var objMinutes;
	if(typeof i == 'undefined')
	{
		objMinutes =  document.getElementById('minutes');
	}
	else
	{
		objMinutes =  document.getElementById('minutes[' + i + ']');
	}
	var time = objMinutes.value;
	time = modifyTimeInput(time);
	objMinutes.value = time;
	onChangeBefore(i);
}
function onChangeProjectName(i)
{
	var objProjectName;
	var objProject;
	if(typeof i == 'undefined')
	{
		objProjectName =  document.getElementById('projectName');
		objProject =  document.getElementById('project');
	}
	else
	{
		objProjectName =  document.getElementById('projectName' + i);
		objProject =  document.getElementById('project[' + i + ']');
	}
	objProject.value = objProjectName.value;
}
function onChangeProject(i)
{
	var objProjectName;
	var objProject;
	if(typeof i == 'undefined')
	{
		objProjectName =  document.getElementById('projectName');
		objProject =  document.getElementById('project');
	}
	else
	{
		objProjectName =  document.getElementById('projectName' + i );
		objProject =  document.getElementById('project[' + i + ']');
	}
	if(IsExitInSelectItem(objProjectName, objProject.value)) objProjectName.value = objProject.value;
	else objProjectName.value = '';
}
function IsExitInSelectItem(objSelect,objItemValue)  
{  
    var isExit = false;  
    for(var i=0;i<objSelect.options.length;i++)  
    {  
        if(objSelect.options[i].value == objItemValue)  
		{  
			isExit = true;  
			break;  
		}  
	}       
	return isExit;  
}  
function onChangeBefore(i,iTime)
{
	var objArray = getObjectOfTime(i);
	var objBefore = objArray[0];
	var objEnd = objArray[1];
	var objMinutes = objArray[2];
	
	var time = objMinutes.value;
	time = modifyTimeInput(time);
	var minutes = timeToMinutes(time);
	var indexDelta = parseInt(minutes/todoTimesDelta);
	objEnd.selectedIndex = (objBefore.selectedIndex + indexDelta)%objEnd.length;
}
function onChangeNext(i)
{
	var objArray = getObjectOfTime(i);
	var objBefore = objArray[0];
	var objEnd = objArray[1];
	var objMinutes = objArray[2];
 
	if(typeof objMinutes != 'undefined')
	{
		var time =objEnd.selectedIndex - objBefore.selectedIndex;
		if(time>0)
		{
			time = time * todoTimesDelta;
		}
		else
		{
			time = 1440 +time * todoTimesDelta;
		}
		objMinutes.value = minutesToTime(time);
	}
}
//minutes 90 to Time: 1:30
function minutesToTime(minutes)
{
	var hour = parseInt(minutes/60);
	var minutes = minutes%60;
	var newTime = hour +":" + minutes;
	return newTime;
}
//minutes  1:30 to Time:90
function timeToMinutes(time)
{
	var timeArray = new Array();
	timeArray = time.split(":");
	if (timeArray.length<2)
	{
		time =  modifyTimeInput(time);
		timeArray = time.split(":");
	}
	var hour = Number(timeArray[0]);
	var minute = Number(timeArray[1]);
	var newTime = hour*60 + minute;
	return newTime;
}
//根据时间值设定选项,如果没有，进行上下靠近选择
function setTimeCombo(sId,Value)
{	
	var s = document.getElementById(sId);
	var ops = s.options;
	for (var i = 0;i<ops.length;i++)
	{
		var tempValue = ops[i].value;
		if(Value <= tempValue)
		{
			ops[i].selected = true;
			break;
		}
	}
}
function setBeginsAndEndsKevin(i, beginOrEnd)
{
    if(typeof i == 'undefined')
    {
		var EndObj;
		var startObj = $("#begins" + 0)[0];
		if(startObj.value < todoWorkStart)setTimeCombo("begins0",todoWorkStart);

		for(var j = 0; j < batchCreateNum; j++)
        {
			var startObj = $("#begins" + j)[0];
			var EndObj = $("#ends" + j)[0];
			
            if(j != 0) startObj.selectedIndex = $("#ends" + (j - 1))[0].selectedIndex;
			if(startObj.value<todoEatingStart)
			{
				setTimeCombo("ends" + j,todoEatingStart);
			}	
			else if(startObj.value<todoEatingEnd && startObj.value>=todoEatingStart)
			{
				setTimeCombo("begins" + j,todoEatingEnd);
 				setTimeCombo("ends" + j,todoWorkEnd);
			}
			else if(startObj.value >= todoEatingEnd && startObj.value <todoWorkEnd)
			{
				setTimeCombo("ends" + j,todoWorkEnd);
			}
			else
			{
				document.getElementById("minutes["+j+"]").value = '2';
				SetIndexOfEnd(j,'2');
			}
			SetMinutesValue(j);
			onChaneTimeFinished(j);
       }
    }
    else
    {
		var ismodifyEnd = false;
		if('begin' == beginOrEnd || 'minutes' == beginOrEnd)
        {
			ismodifyEnd = true;
		}
		var objMinutes;
		if(ismodifyEnd)
		{
			objMinutes = document.getElementById("minutes["+i+"]"); 
			SetIndexOfEnd(i,objMinutes.value);
		}
		SetMinutesValue(i);
		onChaneTimeFinished(i);
 

		for(j = i+1; j < batchCreateNum; j++)
        {
			$("#begins" + j)[0].selectedIndex = $("#ends" + (j - 1))[0].selectedIndex;
			objMinutes = document.getElementById("minutes["+j+"]"); 
			SetIndexOfEnd(j,objMinutes.value);
 			SetMinutesValue(j);
			onChaneTimeFinished(j);
        }
    }
}
function SetIndexOfEnd(i, iTimesDelta)
{
	var time = modifyTimeInput(iTimesDelta);
	var timeArray = new Array();
	timeArray = time.split(":");
	var hour = Number(timeArray[0]);
	var minute = Number(timeArray[1]);
	var indexDelta = parseInt((hour*60 + minute)/ todoTimesDelta);//get delta
	var targetIndex = $("#begins" + i)[0].selectedIndex + indexDelta;
	var ObjEnd = $("#ends" + i)[0];
	targetIndex = targetIndex%ObjEnd.length;
	ObjEnd.selectedIndex = targetIndex;
}
//calculate minutes
function SetMinutesValue(i)
{
	var time = $("#ends" + i)[0].selectedIndex - $("#begins" + i)[0].selectedIndex;
	time = time * todoTimesDelta;//delta
	if(time<0) time = time +1440;
	var hour = parseInt(time/60);
	var minutes = time%60;
	if(hour<0)hour = 0;
	if(minutes<0) minutes = 0;
	
	var array = new Array(hour, minutes);
	var newTime = array.join(":");
	document.getElementById('minutes[' + i +']').value = newTime;
}

//calculate minutes
function SethourstypesColor(i)
{
	if(typeof i == 'undefined')
	i = "";
	var ObjHourstypes = document.getElementById('hourstypes'+i);
	var color1 = '';
	var value = ObjHourstypes.value;
	if('ove' == value)
	{
		color1 = todoHoursCcolorOve;
	}
	else if('hol' == value || 'ann' == value || 'rep' == value)
	{
		color1 = todoHoursCcolorHol;
	}
	ObjHourstypes.style.background = color1;
}
function timeVerificated(workDay, limitDate, i)
{
	if(typeof workDay == 'undefined') return true;
	if('' == workDay) return true;
	//当前修改后的日期
	var workDayArray = workDay.split("-");
	var workDayTime = new Date(workDayArray[0], workDayArray[1]-1, workDayArray[2]);
	var workDayTimes = workDayTime.getTime();
	//当前时间
	var today = new Date();
	var todayTimes = today.getTime();
	var year = (today.getYear() < 1900) ? (today.getYear()+1900) : today.getYear();
	var month = today.getMonth();
	var day = today.getDate();
	//上月份的修改截至日期
	var timeArray = todoEndTime.split(":");
	var endDay = new Date(year, month, todoEndDay, timeArray[0], timeArray[1], timeArray[2]);
	var endTimes = endDay.getTime();
	//上月份最后一天的24点时间戳
	var thisMonthFirstDay = new Date(year, today.getMonth(), 1, 0, 0, 0);
	var lastMonthTimes = thisMonthFirstDay.getTime();;
	//时间判断
	var delta = Math.floor((todayTimes - workDayTimes)/(24*3600*1000));//获得今天时间与要修改的时间间隔天数
	var deltaEnd = todayTimes - endTimes;//获得今天时间与上月考勤修改时间之差,大于0则不能修改上月考勤,小于0可以修改
	var deltaLastMonth = lastMonthTimes - workDayTimes;//判断是否是本月之前的考勤,大于0则是,小于0则否
	var isRight = true;
	if(0 != limitDate)
	{
		if(delta >= limitDate) isRight = false;//如果超过设置的时间间隔
	}
	else
	{
		if(deltaLastMonth > 0)//本月之前的考勤
		{
			if(deltaEnd > 0) isRight = false;
			else
			{
				//获得上月第一天时间
				if(month > 0)
				{
					var lastMonthFirstDay = new Date(year, month-1, 1, 0, 0, 0);
					var lastMonthFirstDayTimes = lastMonthFirstDay.getTime();//获得上月第一天时间
					if(workDayTimes >= lastMonthFirstDayTimes) return true;//上月考勤可以修改
					else isRight = false;
				}
				else
				{
					var lastMonthFirstDay = new Date(year-1, 11, 1, 0, 0, 0);
					var lastMonthFirstDayTimes = lastMonthFirstDay.getTime();//获得上月第一天时间
					if(workDayTimes >= lastMonthFirstDayTimes) return true;//上月考勤可以修改
					else isRight = false;
				}
			}
		}
		else return true;
	}
	if(!isRight) 
	{
		alert("时间已超过修改日期,如有疑问,请联系管理员!");
		if(typeof i == 'undefined')
		{
			array = new Array(year, month+1, day);
			str = array.join("-");
			document.getElementById('date').value = str;
		}
		else
		{
			array = new Array(year, month+1, day);
			str = array.join("-");
			document.getElementById('dates[' + i + ']').value = str;
		}
		return false;
	}
	return true;
}
function InitialBeginsSelect(iDisable)
{
	for(j = 1; j < batchCreateNum; j++)
	{
		$("#begins" + j)[0].disabled=iDisable;
	}
}
function onChangeHolidayType()
{

	isHoliday = document.getElementsByName('holidayType')[0].checked;

	for(j = 0; j < batchCreateNum; j++)
	{
		onChaneTimeFinished(j);
	}
}
function getThisDayholiday()
{
	var thisDay = new Date();
	var d1 = thisDay.getDay();
	isHoliday = (d1 == 0 || d1 == 6);
	var obj1 = document.getElementsByName('holidayType')[0];
	obj1.checked = isHoliday;
}

function onButtonDefaulthours(sag)
{
	var objMinutes;
	for(j = 0; j < batchCreateNum; j++)
	{
		objMinutes = document.getElementById("minutes["+j+"]");
		objMinutes.value =	sag;
	}
	setBeginsAndEndsKevin(0, 'minutes');
}
function setProjectForHoliday(i)
{
	if(typeof i == 'undefined')
	{
		if('hol' == document.getElementById("hourstype").value || 'ann' == document.getElementById("hourstype").value
			|| 'rep' == document.getElementById("hourstype").value)
		{
			var objProject;
			var objProjectName;
			objProject = document.getElementById("project"); 
			objProjectName = document.getElementById("projectName"); 
			objProject.value = projectForHoliday;
			objProjectName.value = projectForHoliday;
		}
	}
	else
	{
		OnChangeHourstypes(i);
		var hourType = document.getElementById("hourstypes"+i).value;
		if('hol' == hourType || 'ann' == hourType || 'rep' == hourType)
		{
			var objProject;
			var objProjectName;
			objProject = document.getElementById("project["+i+"]"); 
			objProjectName = document.getElementById("projectName"+i);
			objProject.value = projectForHoliday;
			objProjectName.value = projectForHoliday;
		}
	}
}
function changeMonth(type)
{
	var currentDate;
	if("+1" === type){
		currentDate = nextMonth;
	}
	else if("-1" === type){
		currentDate = lastMonth;
	}
	else if("+0" === type){
		currentDate = thisMonth;
	}
	var link;
	if('project' === methodName){
		link = createLink('kevinhours', methodName, 'projectID='+currentProjectID + '&type='+currentDate);
	}
	else if('product' === methodName){
		link = createLink('kevinhours', methodName, 'productID='+currentProductID + '&type='+currentDate);
	}
	else{
		link = createLink('kevinhours', methodName, 'type='+currentDate);
	}
	
	location.href=link;
}
function getNewTodoList()
{
	var year = document.getElementById("year").value;
	var month = document.getElementById("month").value;
	var link;
	if('project' === methodName){
		link = createLink('kevinhours', methodName, 'projectID='+currentProjectID + '&type='+year+month);
	}
	else if('product' === methodName){
		link = createLink('kevinhours', methodName, 'productID='+currentProductID + '&type='+year+month);
	}
	else{
		link = createLink('kevinhours', methodName, 'type='+year+month+'&account='+currentAccount+'&dept='+currentdept);
	}
	location.href=link;
}
function hideInactive(type){
if(type==0)
	$('.hideInactive').toggleClass('hidden');
else
	$('.hidextInactive').toggleClass('hidden');
}
function hideindeptmember(type){
//    alert(type);
	if(type==0)
            $('.indeptmember').toggleClass('hidden');
	else
            $('.externalmember').toggleClass('hidden');
}