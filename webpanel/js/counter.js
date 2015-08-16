// Returns the current UTC time in seconds since epoch
function getCurrentTime() {
    "use strict";
	var curDate = new Date();
	return curDate.getTime() / 1000 - (curDate.getTime() / 1000 % 1);
}

function formatSeconds(sex) {
    "use strict";

	if (sex < 1) {
		return "right now...";
	}

	var results = [];

	var oneMinute = 60;
	var oneHour = 60 * oneMinute;
	var oneDay = oneHour * 24;
	var oneWeek = oneDay * 7;
	var oneMonth = oneWeek * 4;
	var oneYear = oneMonth * 12;

	var years = Math.floor(sex / oneYear);
	if (years > 0) { results.push(years + " year" + (years !== 1 ? "s" : "")); }
	sex -= years * oneYear;
	var months = Math.floor(sex / oneMonth);
	if (months > 0) { results.push(months + " month" + (months !== 1 ? "s" : "")); }
	sex -= months * oneMonth;
	var weeks = Math.floor(sex / oneWeek);
	if (weeks > 0) { results.push(weeks + " week" + (weeks !== 1 ? "s" : "")); }
	sex -= weeks * oneWeek;
	var days = Math.floor(sex / oneDay);
	if (days > 0) { results.push(days + " day" + (days !== 1 ? "s" : "")); }
	sex -= days * oneDay;
	var hours = Math.floor(sex / oneHour);
	if (hours > 0) { results.push(hours + " hour" + (hours !== 1 ? "s" : "")); }
	sex -= hours * oneHour;
	var minutes = Math.floor(sex / oneMinute);
	if (minutes > 0) { results.push(minutes + " minute" + (minutes !== 1 ? "s" : "")); }
	sex -= minutes * oneMinute;
	sex = Math.floor(sex);
	results.push(sex + " second" + (sex !== 1 ? "s" : ""));

	return results.join(", ");
}

// Works after timer() to increment a target's value
function resetTarget(target, lastUpdateEpoch) {
    "use strict";
    var counter = document.getElementById(target.id);
	if (counter !== null && counter !== undefined) {
		target = counter;
	}

	while (target.hasChildNodes()) {
		target.removeChild(target.lastChild);
	}
	var secondsToNextUpdate = (lastUpdateEpoch + 60 * 5) - Math.floor(new Date().getTime() / 1000);
    target.appendChild(document.createTextNode(formatSeconds(secondsToNextUpdate)));
}

var targets = {}; // Keeps track of timer intervals so that we can cancel duplicate timers

// Creates a timer that repeatedly decrements a target's value in tandem with resetTarget()
function timer(target, lastUpdateEpoch) {
    "use strict";
	resetTarget(target, lastUpdateEpoch);
    // Clear the previous timer if one existed on an element with the same ID
    if (targets[target.id] !== undefined) {
        clearInterval(targets[target.id]);
    }
    // Start counting
    targets[target.id] = setInterval(function () { resetTarget(target, lastUpdateEpoch); }, 1000);
}

function killTimers() {
	for (targetId in targets) {
		clearInterval(targets[targetId]);
	}
}