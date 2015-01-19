<?php
	$title = "Webpanel Log";
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/panel.css">
		<script type="text/javascript" src="/js/formatting.js"></script>
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="body">
			<h2>Webpanel Log</h2>
			<table id="webpanel-log">
				<tr class="title-row">
					<td>Timestamp</td>
					<td>User</td>
					<td>Message</td>
					<td>Relative Time</td>
				</tr>
			</table>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
		<script type="text/javascript">
			var log = [];
			$.ajaxSetup({async:false});
			$.get("/logging/logger.php?verbose_log", function(data) {
				log = JSON.parse(data).log;
			});
			$.ajaxSetup({async:true});
			log = log.sort(function(a, b) {
				return b.timestamp - a.timestamp;
			});
			var target = document.getElementById("webpanel-log");
			for (var entry in log) {
				var tr = document.createElement("TR");
				var timestamp = document.createElement("TD");
				timestamp.innerHTML = formatDate(log[entry].timestamp);
				var user = document.createElement("TD");
				user.innerHTML = log[entry].user;
				var message = document.createElement("TD");
				message.innerHTML = log[entry].message;
				var relativeTime = document.createElement("TD");
				relativeTime.innerHTML = formatSeconds(new Date().getTime() / 1000 - log[entry].timestamp) + " ago";
				tr.appendChild(timestamp);
				tr.appendChild(user);
				tr.appendChild(message);
				tr.appendChild(relativeTime);
				target.appendChild(tr);
			}
		</script>
	</body>
</html>