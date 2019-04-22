function changeCreateDate(date)
{
	if(timeVerificated(date, limitDate))
	{
		var name = document.getElementById('name').value;
		date = date.replace(/\-/g, '');
		link = createLink('kevinhours', 'create', 'date=' + date + '&account=&name=' + name);
		location.href=link;
	}
}