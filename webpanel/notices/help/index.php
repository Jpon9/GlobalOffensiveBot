<?php
	$title = "Notices Help";
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
			<h2>Notices Help</h2>
			<p>
				The notices can be a bit confusing as they are the most complicated part of the bot configuration.  This page should help.
			</p>
			<h3>Notice Types</h3>
			<p>
				<ul>
					<li>A Recurring Weekly Post is a thread that is posted at the time specified on a weekly basis (e.g. Community Night)</li>
					<li>A Recurring Biweekly Post is a thread that is posted once every other week (e.g. Ander's Workshop Showcase)</li>
					<li>A Nonrecurring Post is a thread that is only posted once, then "Master Disable" is checked afterward.</li>
					<li>A Nonrecurring Link is a link that is linked in the notice but not auto-posted to a thread.  After the first occurrance, it is "Master Disabled."</li>
					<li>A Non-Thread Link is a link that is linked in the notice but not auto-posted to a thread.</li>
				</ul>
			</p>
			<h3>Fields and the types they apply to</h3>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Poster Account</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The Reddit account that will be used to post the thread or link.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Notice Title</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The title that will appear in the notice in the top-right notices section of the subreddit.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Notice Type</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>Type of notice, descriptions are at the top of the page.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Notice Category</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The category that will appear on the individual notices in the top-right notices section of the subreddit.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Starting Time</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The time that the notice will be posted, give or take a bot iteration.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Duration</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The number of hours that the notice will remain active in the top-right notices section of the subreddit. A value of less than 0.5 is not recommended.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Thread Title</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The title of the thread that will be posted by the account specified in Poster Account.</td>
				</tr>
			</table
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Thread Link</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>None, do not edit unless you know what you're doing</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>When a post is auto-posted by a bot account, this is the link to the thread in the top-right notice section of the subreddit.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Body</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>The body of the thread that will be posted if one of the "Applies to..." types is used.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Hide Notice</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>Hides the notice while it is still active.</td>
				</tr>
			</table>
			<table class="notice-help">
				<tr>
					<td>Name of Field</td>
					<td>Master Disable</td>
				</tr>
				<tr>
					<td>Applies to...</td>
					<td>
						<ul>
							<li>Recurring Weekly Post</li>
							<li>Recurring Biweekly Post</li>
							<li>Nonrecurring Post</li>
							<li>Nonrecurring Link</li>
							<li>Non-Thread Link</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td>Disables the notice and any future posting of the thread while this is checked.</td>
				</tr>
			</table>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>