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

function getChildByAttr(parent, attr, value, searchType) {
	searchType = searchType || "";
	if (parent == undefined || parent == null || !parent.hasChildNodes) { return; }
	return $(parent).find("[" + attr + searchType + "='" + value + "']")[0];
}

function getChildByName(container, str) {
	for (var i in container) {
		if (container[i] === null) { continue; }
		if (container[i].name === str) {
			return container[i];
		} else {
			if (container[i].hasChildNodes) {
				getChildByName(container[i].childNodes);
			}
		}
	}
}

/* Generic Field Types */

function select(target, name, options, selected) {
	var select = document.createElement("SELECT");
	select.name = name;
	select.setAttribute("data-original-value", selected);
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
	input.setAttribute("data-original-value", value);
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
	textarea.setAttribute("data-original-value", value);
	textarea.name = name;
	textarea.value = value;
	target.appendChild(textarea);
	return textarea;
}

function checkbox(target, name, checked) {
	var checkbox = document.createElement("INPUT");
	checkbox.setAttribute("data-original-value", checked);
	checkbox.type = "checkbox";
	checkbox.name = name;
	checkbox.checked = checked;
	target.appendChild(checkbox);
	return checkbox;
}

function hidden(target, name, value) {
	var hidden = document.createElement("INPUT");
	hidden.setAttribute("data-original-value", value);
	hidden.type = "hidden";
	hidden.name = name;
	hidden.value = value;
	target.appendChild(hidden);
	return hidden;
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
	div.innerHTML += " ";
	nowButton(div);
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
	div.innerHTML += " ";
	nowButton(div);
	target.appendChild(div);
}

function nowButton(target) {
	var button = document.createElement("BUTTON");
	button.type = "button";
	button.name = "set_time_to_now";
	button.className = "pure-button pure-button-secondary";
	button.innerHTML += "Now";
	button.setAttribute("onclick", "setTimeToNow(this)");
	target.appendChild(button);
}

function setTimeToNow(btn) {
	var now = new Date();
	var day = now.getDay() - 1; // JS returns Sunday = 0, we need Monday = 0
	var hour = now.getHours();
	var mins = now.getMinutes() + 5; // Add 5 for padding
	var nDay = getChildByName(btn.parentNode.childNodes, "notice_start_day");
	var nHour = getChildByName(btn.parentNode.childNodes, "notice_start_hour");
	var nMin = getChildByName(btn.parentNode.childNodes, "notice_start_minute");
	var pDay = getChildByName(btn.parentNode.childNodes, "post_day");
	var pHour = getChildByName(btn.parentNode.childNodes, "post_hour");
	var pMin = getChildByName(btn.parentNode.childNodes, "post_minute");
	if (nDay) { nDay.value = day; }
	if (pDay) { pDay.value = day; }
	if (nHour) { nHour.value = hour; }
	if (pHour) { pHour.value = hour; }
	if (nMin) { nMin.value = mins; }
	if (pMin) { pMin.value = mins; }
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

function hideNotice(target, value) {
	value = value || false;
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Hide notice?");
	checkbox(div, "hide_notice", value);
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

function disablePosting(target, value) {
	value = value || false;
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Disable posting?");
	checkbox(div, "disable_posting", value);
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
	spt.setAttribute("data-original-value", isSelfPost);
	postTypeSelection.appendChild(spt);
	
	// Link post trigger
	var lpt = document.createElement("P");
	lpt.setAttribute("onclick", "lpExpand(this)");
	lpt.className = (!isSelfPost ? "active " : "") + "lp-trigger";
	lpt.innerHTML = "Link Post";
	lpt.setAttribute("data-original-value", isSelfPost);
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

function created(target, value) {
	hidden(target, "created", value);
}

function lastPosted(target, value) {
	hidden(target, "last_posted", value);
}

function lastPostedId(target, value) {
	hidden(target, "last_posted_id", value);
}

function uniqueNoticeId(target, value) {
	hidden(target, "unique_notice_id", value);
}

function isStickied(target, value) {
	hidden(target, "is_stickied", value);
}

function isNewItem(target, value) {
	hidden(target, "is_new_item", value);
}

function resetNotice(target, value) {
	value = value || false;
	var div = document.createElement("DIV");
	div.className = "pure-control-group";
	label(div, "Reset timing?");
	checkbox(div, "reset_timing", value);
	target.appendChild(div);
}