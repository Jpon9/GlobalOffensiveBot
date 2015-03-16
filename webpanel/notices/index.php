<?php
	include_once("./notices.php");

	$title = "Notices";
	$displaySuccess = false;

	// If at least one notice exists in POST, rewrite the stickies.json file
	if (isset($_POST["0_notice_title"])) {
		$i = 0;
		$notices = ["stickies" => []];
		$numOfNotices = intval(substr(array_keys($_POST)[count($_POST) - 1], 0, 1)) + 1;
		for ($i = 0; $i < $numOfNotices; ++$i) {
			$base = $i . "_";
			$postDay = $_POST[$base . "post_day"];
			$postHour = $_POST[$base . "post_hour"] + $_POST["timezone"];
			// Convert times to UTC based on the timezone we got from Javascript
			if ($postHour < 0) {
				$postHour += 24;
				$postDay -= 1;
			} else if ($postHour >= 24) {
				$postHour -= 24;
				$postDay += 1;
			}

			// Build the notice
			$notice = [
				"poster_account" => $_POST[$base . "poster_account"],
				"postedflag" => $_POST[$base . "postedflag"],
				"notice_title" => $_POST[$base . "notice_title"],
				"type" => $_POST[$base . "type"],
				"noticetype" => $_POST[$base . "noticetype"],
				"time" => [
					$postDay,
					$postHour,
					$_POST[$base . "post_minute"]
				],
				"duration_hours" => $_POST[$base . "duration_hours"],
				"post_title" => $_POST[$base . "post_title"],
				"postlink" => $_POST[$base . "postlink"],
				"body" => $_POST[$base . "body"],
				"hide_notice" => isset($_POST[$base . "hide_notice"]),
				"master_disable" => isset($_POST[$base . "master_disable"])
			];
			array_push($notices['stickies'], $notice);
		}
		updateNotices($notices);
		$displaySuccess = true;
		unset($_POST);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/notices.css">
		<script type="text/javascript" src="/js/notices.js"></script>
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body class="pure-g">
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="main" class="pure-u-1 pure-u-lg-4-5">
			<div class="inner">
				<?php
					if ($displaySuccess) {
						include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
					}
				?>
				<h2>Notices</h2>
				<p>Documentation can be found on <a href="/notices/help/">this page.</a></p>
				<form class="pure-form pure-form-aligned">
					<fieldset>
						<div class="pure-control-group">
							<button id="plus-notice" type="button" class="pure-button pure-button-secondary">Add Notice</button>
						</div>
						<div id="notices">
							<h3>Edit Notices</h3>
							<div id="notice-edit" class="pure-g">

							</div>
						</div>
						<div id="pure-controls">
							<button type="button" id="submit-notices" onclick="sendNotices()" class="pure-button pure-button-primary">Submit<img src="/images/loading.png" alt="loading"></button>
							<p id="success" style="display:none">Success! Notices saved.</p>
							<p id="failure" style="display:none">Error! Notices not saved.</p>
						</div>
					</fieldset>
				</form>
				<script type="text/javascript">
					var notices;
					$.ajaxSetup({async:false});
					$.get("/notices/notices.php?verbose", function(data) {
						notices = JSON.parse(data).notices;
					});
					$.ajaxSetup({async:true});
					target = document.getElementById("notice-edit");
					var numOfNotices = 0;
					for (var notice in notices) {
						var dn = notices[notice];
						var nodes = addNotice(notices[notice]);
						// Fill in the nodes with the appropriate data
					}

					// Remove text nodes
					for (var i = 0; i < target.parentElement.childNodes.length; ++i) {
						if (target.parentElement.childNodes[i].nodeType == 3) {
							target.parentElement.removeChild(target.parentElement.childNodes[i]);
						}
					}

					// Bind the buttons
					$("#plus-notice").on("click", function(ev) {
						addNotice();
					});

					function addNotice(notice) {
						if (notice === undefined) {
							// Default notice values
							notice = {
								"type": "autopost+notice",
								"category": "notice",
								"thread_title": "Default Thread Title",
								"notice_title": "Default Notice Title",
								"thread_link": "https://reddit.com/r/GlobalOffensive",
								"poster_account": "GlobalOffensiveBot",
								"notice_start_time": [0, 0, 0],
								"post_time": [0, 0, 0],
								"notice_duration": 6,
								"permanent_notice": false,
								"frequency": "once",
								"created": parseInt(new Date().getTime() / 1000),
								"body": "Default thread body.",
								"sticky_duration": 6,
								"permanent_sticky": false,
								"notice_link": "#swag",
								"self_post": true
							}
						}

						var noticeContainer = document.createElement("DIV");
						noticeContainer.className = "notice pure-u-1-2";
						
						var innerNoticeContainer = document.createElement("DIV");
						innerNoticeContainer.className = "inner-2";

						var deleteImage = document.createElement("IMG");
						deleteImage.setAttribute("onclick", "deleteNotice(this)");
						deleteImage.src = "/images/delete-hover.png";
						deleteImage.className = "delete";
						deleteImage.alt = "delete";
						innerNoticeContainer.appendChild(deleteImage);

						var noticeH4 = document.createElement("H4");
						noticeH4.setAttribute("onclick", "collapse(this)");
						noticeH4.setAttribute("data-collapsed", "true");
						noticeH4.className = "notice-title";
						var titleImage = document.createElement("IMG");
						titleImage.src = "/images/collapse.png";
						titleImage.className = "arrow collapsed";
						titleImage.alt = "collapse";
						noticeH4.appendChild(titleImage);
						if (notice['notice_title'] !== "Default Notice Title") {
							noticeH4.innerHTML += notice['notice_title'];
						} else if (notice['thread_title'] !== "Default Thread Title") {
							noticeH4.innerHTML += notice['thread_title'];
						} else {
							noticeH4.innerHTML += "New Scheduled Item";
						}
						innerNoticeContainer.appendChild(noticeH4);

						var collapsible = document.createElement("DIV");
						collapsible.className = "collapsible";

						var innerCollapsible = document.createElement("DIV");
						innerCollapsible.className = "inner-2";

						type(innerCollapsible, notice['type']);

						// Notice fields
						var nF = document.createElement("DIV");
						nF.className = "notice-fields";
						// Thread fields
						var tF = document.createElement("DIV");
						tF.className = "thread-fields";

						// Hides fields you don't care about
						if (notice['type'] === 'autopost') {
							nF.style.display = "none";
						} else if (notice['type'] === 'notice') {
							tF.style.display = "none";
						}
						
						// Build the fields of the item
						frequency(innerCollapsible, notice['frequency']);
						category(nF, notice['category']);
						noticeTitle(nF, notice['notice_title']);
						noticeLink(nF, notice['notice_link'], notice['type'] !== 'notice');
						noticeStartTime(nF, notice['notice_start_time']);
						permanentNotice(nF, notice['permanent_notice']);
						noticeDuration(nF, notice['notice_duration']);
						threadTitle(tF, notice['thread_title']);
						posterAccount(tF, notice['poster_account']);
						postTime(tF, notice['post_time']);
						textOrLinkPost(tF, notice['self_post'],
							notice['sticky_duration'],
							notice['permanent_sticky'],
							notice['body'],
							notice['thread_link']);

						innerCollapsible.appendChild(nF);
						innerCollapsible.appendChild(tF);

						collapsible.appendChild(innerCollapsible);
						innerNoticeContainer.appendChild(collapsible);
						noticeContainer.appendChild(innerNoticeContainer);
						target.appendChild(noticeContainer);

						notice['notice_start_time'] = convertTzToLocal(notice['notice_start_time']);
						notice['post_time'] = convertTzToLocal(notice['post_time']);
					}

					function convertTzToLocal(time) {
						// Convert post time from UTC to local time
						time[1] -= new Date().getTimezoneOffset() / 60;
						// Convert times to UTC based on the timezone we got from Javascript
						if (time[1] < 0) {
							time[1] += 24;
							time[0] -= 1;
						} else if (time[1] >= 24) {
							time[1] -= 24;
							time[0] += 1;
						}
						return time;
					}

					function deleteNotice(trigger) {
						var noticeContainerFound = false;
						var suspect = trigger;
						while (!noticeContainerFound) {
							if (suspect.parentNode.className === "notice pure-u-1-2") {
								noticeContainerFound = true;
							}
							suspect = suspect.parentNode;
						}
						$(suspect).fadeOut(function() {
							suspect.parentNode.removeChild(suspect);
						});
					}

					function collapse(elem) {
						var isCollapsing = false;
						if (elem.getAttribute("data-collapsed") === null) {
							elem.setAttribute("data-collapsed", "false");
						} else if (elem.getAttribute("data-collapsed") == "false") {
							isCollapsing = true;
							elem.setAttribute("data-collapsed", "true");
						} else {
							elem.setAttribute("data-collapsed", "false");
						}

						var index = 0;
						for (e in elem.parentNode.childNodes) {
							if (elem.parentNode.childNodes[e].className == "collapsible") {
								index = e;
							}
						}

						$(elem.parentNode.childNodes[index]).slideToggle();
						elem.childNodes[0].className = "arrow " + (isCollapsing ? "" : "un") + "collapsed";
					}

					function lpExpand(elem) {
						var indexes = getPostGroupIndexes(elem);

						var sp = indexes[0];
						var lp = indexes[1];
						var spTrigger = indexes[2];

						elem.parentNode.childNodes[spTrigger].className = "sp-trigger";
						elem.className = "active lp-trigger";

						animateGroupSwitch(elem.parentNode.childNodes[lp],
							elem.parentNode.childNodes[sp]);
					}

					function spExpand(elem) {
						var indexes = getPostGroupIndexes(elem);

						var sp = indexes[0];
						var lp = indexes[1];
						var lpTrigger = indexes[2];

						elem.parentNode.childNodes[lpTrigger].className = "lp-trigger";
						elem.className = "active sp-trigger";

						animateGroupSwitch(elem.parentNode.childNodes[sp],
							elem.parentNode.childNodes[lp]);
					}

					function getPostGroupIndexes(elem) {
						var sp = 0, lp = 0, otherTrigger = 0;
						
						for (e in elem.parentNode.childNodes) {
							if (elem.parentNode.childNodes[e].className === "self-post post-type") {
								sp = e;
							} else if (elem.parentNode.childNodes[e].className === "link-post post-type") {
								lp = e;
							} else if (("" + elem.parentNode.childNodes[e].className).search("active") !== -1) {
								otherTrigger = e;
							}
						}

						return [sp, lp, otherTrigger];
					}

					function animateGroupSwitch(expanding, collapsing) {
						$(collapsing).slideUp(250, function() {
							$(expanding).slideDown(250);
						});
					}

					function changeType(trigger) {
						var newType = trigger.options[trigger.selectedIndex].value;
						var nF = undefined, tF = undefined;
						for (var node in trigger.parentNode.parentNode.childNodes) {
							if (trigger.parentNode.parentNode.childNodes[node].className === "notice-fields") {
								nF = trigger.parentNode.parentNode.childNodes[node];
							} else if (trigger.parentNode.parentNode.childNodes[node].className === "thread-fields") {
								tF = trigger.parentNode.parentNode.childNodes[node];
							}
							if (nF !== undefined && tF !== undefined) {
								break;
							}
						}
						if (nF === undefined || tF === undefined) {
							console.error("Could not find both item type groupings");
							return;
						}
						if (newType === "autopost+notice") {
							nF.style.display = "block";
							tF.style.display = "block";
							$(nF).find("[name='notice_link']").parent().hide()
						} else if (newType === "autopost") {
							nF.style.display = "none";
							tF.style.display = "block";
						} else if (newType === "notice") {
							nF.style.display = "block";
							tF.style.display = "none";
							$(nF).find("[name='notice_link']").parent().show()
						}
					}

					function getChildByName(container, str) {
						//console.log(container);
						for (var i in container) {
							if (container[i] === null) { continue; }
							console.log(container[i]);
							if (container[i].name === str) {
								return container[i];
							} else {
								if (container[i].hasChildNodes) {
									getChildByName(container[i].childNodes);
								}
							}
						}
					}

					function getChildByAttr(parent, attr, value, searchType) {
						searchType = searchType || "";
						if (parent == undefined || parent == null || !parent.hasChildNodes) { return; }
						return $(parent).find("[" + attr + searchType + "='" + value + "']")[0];
					}

					function sendNotices() {
						var parent = document.getElementById("notice-edit");
						var notices = [];
						$("#submit-notices img").animate({width: '16px'}, 250);
						for (var i in parent.childNodes) {
							if (parent.childNodes[i] === undefined) { continue; }
							if (!parent.childNodes[i].nodeType) { continue; }
							if (parent.childNodes[i].nodeType != 1) { continue; }
							var notice = {};
							notice.type = getChildByAttr(parent.childNodes[i], 'name', 'type').value;
							notice.frequency = getChildByAttr(parent.childNodes[i], 'name', 'frequency').value;
							notice.category = getChildByAttr(parent.childNodes[i], 'name', 'category').value;
							notice.notice_title = getChildByAttr(parent.childNodes[i], 'name', 'notice_title').value;
							notice.notice_link = getChildByAttr(parent.childNodes[i], 'name', 'notice_link').value;
							notice.notice_start_time = [
								getChildByAttr(parent.childNodes[i], 'name', 'notice_start_day').value,
								getChildByAttr(parent.childNodes[i], 'name', 'notice_start_hour').value,
								getChildByAttr(parent.childNodes[i], 'name', 'notice_start_minute').value
							];
							notice.permanent_notice = getChildByAttr(parent.childNodes[i], 'name', 'permanent_notice').checked;
							notice.notice_duration = getChildByAttr(parent.childNodes[i], 'name', 'notice_duration').value;
							notice.thread_title = getChildByAttr(parent.childNodes[i], 'name', 'thread_title').value;
							notice.poster_account = getChildByAttr(parent.childNodes[i], 'name', 'poster_account').value;
							notice.post_time = [
								getChildByAttr(parent.childNodes[i], 'name', 'post_day').value,
								getChildByAttr(parent.childNodes[i], 'name', 'post_hour').value,
								getChildByAttr(parent.childNodes[i], 'name', 'post_minute').value
							];
							notice.self_post = getChildByAttr(parent.childNodes[i], 'class', 'active', '*').innerHTML === "Self-Post";
							notice.sticky_duration = getChildByAttr(parent.childNodes[i], 'name', 'sticky_duration').value;
							notice.permanent_sticky = getChildByAttr(parent.childNodes[i], 'name', 'permanent_sticky').checked;
							notice.body = getChildByAttr(parent.childNodes[i], 'name', 'body').value;
							notice.thread_link = getChildByAttr(parent.childNodes[i], 'name', 'thread_link').value;
							notice.created = parseInt(new Date().getTime() / 1000);
							notices.push(notice);
						}
						$.ajax({
							type: 'POST',
							url: 'notices.php',
							data: {'notices': JSON.stringify(notices)},
							success: function() {
								$("#submit-notices img").animate({width: '0'}, 250, function() {
									$("#success").fadeIn(250, function() {
										console.log("Scheduling fadeout");
										setTimeout(function() {
											$("#success").fadeOut(500);
										}, 2500);
									});
								});
							},
							failure: function() {
								$("#submit-notices img").animate({width: '0'}, 250, function() {
									$("#failure").fadeIn(250, function() {
										setTimeout(function() {
											$("#failure").fadeOut(500);
										}, 2500);
									});
								});
							}
						});
					}
				</script>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>