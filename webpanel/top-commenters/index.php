<?php
	$title = "Top Commenters";
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
				<h2>Top Commenters (January 2015)</h2>
				<table id="giveaway-winners">
					<tr class="title-row">
						<td>#</td>
						<td>Reddit Account</td>
						<td>Number of Comments</td>
					</tr>
				</table>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
		<script type="text/javascript">
			var commenters = [];
			$.ajaxSetup({async:false});
			$.get("./getCommenters.php?verbose", function(data) {
				commenters = JSON.parse(data);
			});
			$.ajaxSetup({async:true});
			var target = document.getElementById("giveaway-winners");
			var i = 0;
			for (var c in commenters) {
				i += 1;
				var tr = document.createElement("TR");
				var index = document.createElement("TD");
				index.innerHTML = i;
				var redditAccount = document.createElement("TD");
				redditAccountLink = document.createElement("A");
				redditAccountLink.href = "http://reddit.com/user/" + commenters[c][0];
				redditAccountLink.innerHTML = commenters[c][0];
				redditAccount.appendChild(redditAccountLink);
				var numOfComments = document.createElement("TD");
				numOfComments.innerHTML = commenters[c][1];
				tr.appendChild(index);
				tr.appendChild(redditAccount);
				tr.appendChild(numOfComments);
				target.appendChild(tr);

				if (i > 9999) {
					break;
				}
			}
		</script>
	</body>
</html>