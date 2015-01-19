<?php
	$title = "Giveaway";
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/panel.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="body">
			<h2>Giveaway Winners</h2>
			<table id="giveaway-winners">
				<tr class="title-row">
					<td>#</td>
					<td>Reddit Account</td>
					<td>Steam Profile</td>
				</tr>
			</table>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
		<script type="text/javascript">
			var winners = [];
			$.ajaxSetup({async:false});
			$.get("./getWinners.php?verbose", function(data) {
				winners = JSON.parse(data);
			});
			$.ajaxSetup({async:true});
			var target = document.getElementById("giveaway-winners");
			var i = 0;
			for (var winner in winners) {
				i += 1;
				var tr = document.createElement("TR");
				var index = document.createElement("TD");
				index.innerHTML = i;
				var redditAccount = document.createElement("TD");
				redditAccountLink = document.createElement("A");
				redditAccountLink.href = "http://reddit.com/user/" + winners[winner].reddit_username;
				redditAccountLink.innerHTML = winners[winner].reddit_username;
				redditAccount.appendChild(redditAccountLink);
				var steamProfile = document.createElement("TD");
				steamProfileLink = document.createElement("A");
				steamProfileLink.href = winners[winner].steam_profile;
				steamProfileLink.innerHTML = "Profile Link";
				steamProfile.appendChild(steamProfileLink);
				tr.appendChild(index);
				tr.appendChild(redditAccount);
				tr.appendChild(steamProfile);
				target.appendChild(tr);
			}
		</script>
	</body>
</html>