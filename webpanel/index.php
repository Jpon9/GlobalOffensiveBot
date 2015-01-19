<?php
	$title = "Home";
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
			<h2>The GlobalOffensiveBot Webpanel</h2>
			<p>
				This is the place for you, an authenticated /r/GlobalOffensive moderator, to edit the settings for the bot that controls and automatically updates the sidebar of the subreddit.  Choose what you want to configure in the sidebar and start editing.  GLHF!
			</p>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>