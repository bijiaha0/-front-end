function changeTodoDate(date)
{
	date = date.replace(/\-/g, '');
	var link = createLink('kevinhours','todo','date=' + date);
	location.href=link;
}