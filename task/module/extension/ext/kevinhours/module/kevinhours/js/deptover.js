function getNewOverList(deptID)
{
	var year = document.getElementById("year").value;
	link = createLink('kevinhours', 'deptover', 'type='+year+'&deptID='+deptID);
	location.href=link;
}