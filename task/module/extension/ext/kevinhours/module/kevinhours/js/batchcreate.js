function changeBatchCreateDate(date)
{
	if(timeVerificated(date, limitDate))
	{
		date = date.replace(/\-/g, '');
		link = createLink('kevinhours', 'batchCreate', 'date=' + date);
		if(isOnlyBody){
			link =link + '?onlybody=yes';
		}
		location.href=link;
	}
}

function updateAction(date)
{
  if(date.indexOf('-') != -1)
  {
    var datearray = date.split("-");
    var date = '';
    for(i=0 ; i<datearray.length ; i++)
    {
      date = date + datearray[i];
    }
  }
  link = createLink('kevinhours', 'batchCreate', 'date=' + date);
  location.href=link;
}

function switchDateList(number)
{
    if($('#switchDate' + number).attr('checked') == 'checked')
    {
        $('#begins' + number).attr('disabled', 'disabled');
        $('#ends' + number).attr('disabled', 'disabled');
    }
    else
    {
        $('#begins' + number).removeAttr('disabled');
        $('#ends' + number).removeAttr('disabled');
    }
}
