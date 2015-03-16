/* Utilities and reference */

function padNumber(num) {
	if (num < 10) {
		return "0" + num;
	}
	return num;
}

var days = {
	"0": "Monday",
	"1": "Tuesday",
	"2": "Wednesday",
	"3": "Thursday",
	"4": "Friday",
	"5": "Saturday",
	"6": "Sunday"	
}

var hours = {};
var minutes = {};

for (var i = 0; i < 24; i++) { hours[i] = padNumber(i); }
for (var i = 0; i < 60; i++) { minutes[i] = padNumber(i); }

/* Generic Field Types */

function select(target, name, options, selected) {
	var select = document.createElement("SELECT");
	select.name = name;
	for (var i in options) {
		var option = document.createElement("OPTION");
		option.value = i;
		option.innerHTML = options[i];
		if (i == selected) {
			option.setAttribute("selected", "selected");
		}
		select.appendChild(option);
	}
	target.appendChild(select);
	return select;
}

function textInput(target, name, value) {
	value = value || "";

	var input = document.createElement("INPUT");
	input.type = "text";
	input.name = name;
	input.value = value;
	target.appendChild(input);
	return textInput;
}

function label(target, text) {
	var label = document.createElement("LABEL");
	label.innerHTML = text;
	target.appendChild(label);
	return label;
}

function textarea(target, name, value) {
	var textarea = document.createElement("TEXTAREA");
	textarea.name = name;
	textarea.value = value;
	target.appendChild(textarea);
	return textarea;
}

function checkbox(target, name, checked) {
	var checkbox = document.createElement("INPUT");
	checkbox.type = "checkbox";
	checkbox.name = name;
	checkbox.checked = checked;
	target.appendChild(checkbox);
	return checkbox;
}

/* Individual Fields */

function type(target, value) {
	value = value || "autopost+notice";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	var sel = select(div,
		"type", {
			"autopost+notice": "Autoposted Thread + Notice",
			"autopost": "Autoposted Thread Only",
			"notice": "Notice Only"
		},
		value
	);
	sel.setAttribute("onclick", "changeType(this)");
	target.appendChild(div);
}

function category(target, value) {
	value = value || "notice";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Notice Category");
	select(div,
		"category",
		{
			"notice": "Notice",
			"discussion": "Discussion",
			"event": "Event"
		},
		value
	);
	target.appendChild(div);
}

function threadTitle(target, value) {
	value = value || "Default Title";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Thread Title");
	textInput(div, "thread_title", value);
	target.appendChild(div);
}

function noticeTitle(target, value) {
	value = value || "Default Title";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Notice Title");
	textInput(div, "notice_title", value);
	target.appendChild(div);
}

function posterAccount(target, value) {
	value = value || "GlobalOffensiveBot";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Poster Account");
	select(div,
		"poster_account",
		{
			"GlobalOffensiveBot": "GlobalOffensiveBot",
			"csgocomnights": "csgocomnights"
		},
		value
	);
	target.appendChild(div);
}

function postTime(target, value) {
	value = value || [0, 0, 0];
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Post Time");
	select(div, "post_day", days, value[0]);
	div.innerHTML += " ";
	select(div, "post_hour", hours, value[1]);
	div.innerHTML += " ";
	select(div, "post_minute", minutes, value[2]);
	target.appendChild(div);
}

function noticeStartTime(target, value) {
	value = value || [0, 0, 0];
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Notice Start Time");
	select(div, "notice_start_day", days, value[0]);
	div.innerHTML += " ";
	select(div, "notice_start_hour", hours, value[1]);
	div.innerHTML += " ";
	select(div, "notice_start_minute", minutes, value[2]);
	target.appendChild(div);
}

function noticeDuration(target, value) {
	value = value || 6;
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Notice Duration (hours)");
	textInput(div, "notice_duration", value);
	target.appendChild(div);
}

function permanentNotice(target, value) {
	value = value || false;
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Permanent notice?");
	checkbox(div, "permanent_notice", value);
	target.appendChild(div);
}

function noticeLink(target, value, hidden) {
	var noticeLink = document.createElement("DIV");
	noticeLink.className = "pure-control-group";
	noticeLink.style.display = hidden ? "none" : "block";
	label(noticeLink, "Notice Link");
	textInput(noticeLink, "notice_link", value);
	target.appendChild(noticeLink);
}

function frequency(target, value) {
	value = value || "weekly";
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Frequency");
	select(div,
		"frequency",
		{
			"once": "Once",
			"daily": "Daily",
			"weekly": "Weekly",
			"biweekly": "Biweekly",
			"monthly": "Monthly"
		},
		value
	);
	target.appendChild(div);
}

function textOrLinkPost(target, isSelfPost, spStickyDuration, spPermSticky, spBody, lpLink) {
	spStickyDuration = spStickyDuration || 6;
	spBody = spBody || "Default self-post body.";
	lpLink = lpLink || "https://reddit.com/r/GlobalOffensive/";
	var postTypeSelection = document.createElement("DIV");
	postTypeSelection.className = "post-type-selection";
	
	// Self-post trigger
	var spt = document.createElement("P");
	spt.setAttribute("onclick", "spExpand(this)");
	spt.className = (isSelfPost ? "active " : "") + "sp-trigger";
	spt.innerHTML = "Self-Post";
	postTypeSelection.appendChild(spt);
	
	// Link post trigger
	var lpt = document.createElement("P");
	lpt.setAttribute("onclick", "lpExpand(this)");
	lpt.className = (!isSelfPost ? "active " : "") + "lp-trigger";
	lpt.innerHTML = "Link Post";
	postTypeSelection.appendChild(lpt);

	// Self post
	var selfPost = document.createElement("DIV");
	selfPost.className = "self-post post-type";
	selfPost.style.display = isSelfPost ? "block" : "none";

	var stickyDuration = document.createElement("DIV");
	stickyDuration.className = "pure-control-group";
	label(stickyDuration, "Sticky for (hours)");
	textInput(stickyDuration, "sticky_duration", spStickyDuration);
	selfPost.appendChild(stickyDuration);

	var permSticky = document.createElement("DIV");
	permSticky.className = "pure-control-group";
	label(permSticky, "Permanent sticky?");
	checkbox(permSticky, "permanent_sticky", spPermSticky);
	selfPost.appendChild(permSticky);

	var threadBody = document.createElement("DIV");
	threadBody.className = "pure-control-group";
	label(threadBody, "Thread Body");
	textarea(threadBody, "body", spBody);
	selfPost.appendChild(threadBody);

	postTypeSelection.appendChild(selfPost);

	// Link post
	var linkPost = document.createElement("DIV");
	linkPost.className = "link-post post-type";
	linkPost.style.display = !isSelfPost ? "block" : "none";

	var threadLink = document.createElement("DIV");
	threadLink.className = "pure-control-group";
	label(threadLink, "Thread Link");
	textInput(threadLink, "thread_link", lpLink);
	linkPost.appendChild(threadLink);

	postTypeSelection.appendChild(linkPost);
	target.appendChild(postTypeSelection);
}