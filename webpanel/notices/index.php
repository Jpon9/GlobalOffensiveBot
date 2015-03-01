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
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/panel.css">
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
				<p>
					Documentation can be found on <a href="/notices/help/">this page.</a>
				</p>
				<form action="./" method="POST">
					<input type="hidden" name="timezone" id="timezone" value="">
					<table>
						<tr>
							<td class="button-row"><input id="plus-notice" type="button" value="Add Notice"><input id="minus-notice" type="button" value="Subtract Notice"></td>
						</tr>
					</table>
					<table id="final-table">
						<tr id="final-row">
							<td><input type="submit" value="Submit"></td>
						</tr>
					</table>
					<script type="text/javascript">
						var notices;
						$.ajaxSetup({async:false});
						$.get("/notices/notices.php?verbose", function(data) {
							notices = JSON.parse(data).stickies;
						});
						$.ajaxSetup({async:true});
						console.log(notices);
						target = document.getElementById("final-table");
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

						// Update the time zone field
						document.getElementById("timezone").value = new Date().getTimezoneOffset() / 60;

						// Bind the buttons
						$("#plus-notice").on("click", function(ev) {
							addNotice();
						});
						$("#minus-notice").on("click", function(ev) {
							subtractNotice();
						});

						function generateInput(labelText, name, type, defaultValue, options) {
							var tr = document.createElement("TR");
							var td1 = document.createElement("TD");
							var td2 = document.createElement("TD");
							var label = document.createElement("LABEL");
							label.innerHTML = labelText;
							td1.appendChild(label);
							var input;
							if (type == "select") {
								input = document.createElement("SELECT");
								for (option in options) {
									var opt = document.createElement("OPTION");
									opt.innerHTML = options[option].text;
									opt.value = options[option].value;
									if (options[option].value === defaultValue) {
										opt.selected = true;
									}
									input.appendChild(opt);
								}
							} else if (type == "textarea") {
								input = document.createElement("TEXTAREA");
								input.innerHTML = defaultValue;
							} else if (type == "checkbox") {
								input = document.createElement("INPUT");
								if (defaultValue) {
									input.checked = true;
								}
								input.type = type;
							} else if (type == "hidden") {
								var hiddenInput = document.createElement("INPUT");
								hiddenInput.value = defaultValue;
								hiddenInput.type = type;
								hiddenInput.className = name;
								hiddenInput.name = numOfNotices + "_" + name;
								return hiddenInput;
							} else {
								input = document.createElement("INPUT");
								input.value = defaultValue;
								input.type = type;
							}
							input.name = numOfNotices + "_" + name;
							input.className = name;
							td2.appendChild(input);
							tr.appendChild(td1);
							tr.appendChild(td2);

							return tr;
						}

						function addNotice(notice) {
							if (notice === undefined) {
								notice = {
									"poster_account": "GlobalOffensiveBot",
									"postedflag": false,
									"notice_title": "Default Notice Title",
									"type": "link",
									"noticetype": "discussion",
									"time": [
										0,
										0,
										0
									],
									"duration_hours": 3,
									"post_title": "Default Post Title",
									"postlink": "#default-thread-link",
									"body": "This is a default thread body.",
									"hide_notice": false,
									"master_disable": false
								}
							}

							// Convert post time from UTC to local time
							notice.time[1] -= new Date().getTimezoneOffset() / 60;
							// Convert times to UTC based on the timezone we got from Javascript
							if (notice.time[1] < 0) {
								notice.time[1] += 24;
								notice.time[0] -= 1;
							} else if (notice.time[1] >= 24) {
								notice.time[1] -= 24;
								notice.time[0] += 1;
							}

							// Table for the notice
							var table = document.createElement("TABLE");
							table.className = "notice-edit";
							table.appendChild(generateInput("Currently Posted", "postedflag", "hidden", notice.postedflag));
							table.appendChild(generateInput("Poster Account", "poster_account", "select", notice.poster_account, [
								{"text":"GlobalOffensiveBot","value":"GlobalOffensiveBot"},
								{"text":"csgocomnights","value":"csgocomnights"}
							]));
							table.appendChild(generateInput("Notice Title", "notice_title", "text", notice.notice_title));
							table.appendChild(generateInput("Notice Type", "type", "select", notice.type, [
								{"text":"Recurring Weekly Post","value":"recurring_event"},
								{"text":"Recurring Biweekly Post","value":"recurring_biweekly_event"},
								{"text":"Nonrecurring Post","value":"post_non_recurring"},
								{"text":"Nonrecurring Link","value":"link_non_recurring"},
								{"text":"Non-Thread Link","value":"link"}
							]));
							table.appendChild(generateInput("Notice Category", "noticetype", "select", notice.noticetype, [
								{"text":"Event","value":"event"},
								{"text":"Discussion","value":"discussion"},
								{"text":"Notice","value":"notice"}
							]));
							// POSTING DAY
							var post_day = document.createElement("SELECT");
							post_day.name = numOfNotices + "_post_day";
							var j = 0;
							var days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
							for (var day in days) {
								var option = document.createElement("OPTION");
								option.value = j;
								option.innerHTML = days[day];
								if (j === notice.time[0]) {
									option.selected = true;
								}
								post_day.appendChild(option);
								j++;
							}
							// POSTING HOUR
							var post_hour = document.createElement("SELECT");
							post_hour.name = numOfNotices + "_post_hour";
							for (var i = 0; i < 24; ++i) {
								var option = document.createElement("OPTION");
								option.value = i;
								option.innerHTML = (i < 10 ? "0" + i : i);
								if (i === notice.time[1]) {
									option.selected = true;
								}
								post_hour.appendChild(option);
							}
							// POSTING MINUTE
							var post_minute = document.createElement("SELECT");
							post_minute.name = numOfNotices + "_post_minute";
							for (var i = 0; i < 60; ++i) {
								var option = document.createElement("OPTION");
								option.value = i;
								option.innerHTML = (i < 10 ? "0" + i : i);
								if (i === notice.time[2]) {
									option.selected = true;
								}
								post_minute.appendChild(option);
							}
							var tr = document.createElement("TR");
							var td1 = document.createElement("TD");
							var td2 = document.createElement("TD");
							var label = document.createElement("LABEL");
							label.innerHTML = "Starting Time";
							td1.appendChild(label);
							td2.appendChild(post_day);
							td2.appendChild(post_hour);
							td2.appendChild(post_minute);
							var localTimeMsg = document.createElement("SPAN");
							localTimeMsg.className = "local_time_msg";
							localTimeMsg.innerHTML = "(your local time)";
							td2.appendChild(localTimeMsg);
							tr.appendChild(td1);
							tr.appendChild(td2);
							table.appendChild(tr);
							table.appendChild(generateInput("Duration (hrs)", "duration_hours", "text", notice.duration_hours));
							table.appendChild(generateInput("Thread Title", "post_title", "text", notice.post_title));
							table.appendChild(generateInput("Thread Link", "postlink", "text", notice.postlink));
							table.appendChild(generateInput("Thread Body", "body", "textarea", notice.body));
							table.appendChild(generateInput("Hide Notice", "hide_notice", "checkbox", notice.hide_notice));
							table.appendChild(generateInput("Master Disable", "master_disable", "checkbox", notice.master_disable));
							target.parentElement.insertBefore(table, target);
							numOfNotices++;
						}

						function subtractNotice() {
							if (numOfNotices < 1) { return; }
							target.parentElement.removeChild(target.parentElement.childNodes[numOfNotices + 1]);
							numOfNotices--;
						}
					</script>
				</form>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>