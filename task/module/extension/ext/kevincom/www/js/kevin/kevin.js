function GetNewPeriodMonth(type,year,month)
{
	var currentDate;
	if("+1" === type){
		if(month=='12'){
			month = '01';
			year = ''+ ( parseInt(year) +1);
		}
		else{
			month = ''+ ( parseInt(month) +1)
			if(month.length== 1) month = '0' + month;
		}
		currentDate = 	year + month;
	}
	else if("-1" === type){
		if(month=='01'){
			month = '12';
			year =  ''+( parseInt(year) -1);
		}
		else{
			month = ''+ ( parseInt(month) -1);
			if(month.length== 1) month = '0' + month;
		}
		currentDate = 	year + month;
	}
	else{
		currentDate = 	year + month;
	}
	return currentDate;
}