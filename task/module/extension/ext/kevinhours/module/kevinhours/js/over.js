if($("#slipicon").hasClass('icon-caret-right')){
	$(".side").css('width','');
	$(".main").css('margin-left','');
	$(".main").resize();
}
function getNewOverList()
{
	var year = document.getElementById("year").value;
	link = createLink('kevinhours', 'over', 'type='+year+'&account='+currentAccount+'&deptID=' + currentdeptID);
	location.href=link;
}
function gotoOtherDept(deptID)
{
	link = createLink('kevinhours', 'overdept', 'type='+type+'&deptID='+ deptID);
	location.href = link;
}