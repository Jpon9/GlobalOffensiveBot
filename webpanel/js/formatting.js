function formatDate(epoch) {
	var time = new Date(epoch * 1000);
	var month = time.getMonth();
	switch(month) {
	case 0:
		month = "January";
		break;
	case 1:
		month = "February";
		break;
	case 2:
		month = "March";
		break;
	case 3:
		month = "April";
		break;
	case 4:
		month = "May";
		break;
	case 5:
		month = "June";
		break;
	case 6:
		month = "July";
		break;
	case 7:
		month = "August";
		break;
	case 8:
		month = "September";
		break;
	case 9:
		month = "October";
		break;
	case 10:
		month = "November";
		break;
	case 11:
		month = "December";
		break;
	}
	return time.getDate() + " "
        + month + " "
        + time.getFullYear() + " at "
		+ time.getHours() + ":"
        + padDateNumber(time.getMinutes()) + ":"
        + padDateNumber(time.getSeconds());
}

// Utility function, makes the date look more properly formatted
function padDateNumber(num) {
    return (num < 10) ? "0" + num : "" + num;
}