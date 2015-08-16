<?php
	include_once("./basic_settings.php");

	$title = "Basic Settings";
	$displaySuccess = false;

	if (isset($_POST['target_subreddit']) &&
		isset($_POST['update_timeout']) &&
	    isset($_POST['max_streams_shown']) &&
	    isset($_POST['max_games_shown']) &&
	    isset($_POST['stream_thumbnail_css_name']) &&
	    isset($_POST['spotlight_rotation_timeout']) &&
	    isset($_POST['num_of_headers']) &&
	    isset($_POST['google_api_key']) &&
	    isset($_POST['gosugamers_api_key']) &&
	    isset($_POST['steam_api_key'])) {
		updateSettings($_POST['target_subreddit'],
					   $_POST['update_timeout'],
					   $_POST['max_streams_shown'],
					   $_POST['max_games_shown'],
					   $_POST['stream_thumbnail_css_name'],
					   $_POST['spotlight_rotation_timeout'],
					   $_POST['num_of_headers'],
					   $_POST['google_api_key'],
					   $_POST['gosugamers_api_key'],
					   $_POST['steam_api_key'],
					   isset($_POST['minify_stylesheet']));
		$displaySuccess = true;
		unset($_POST);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/basic.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body class="pure-g">
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="main" class="pure-u-1 pure-u-lg-4-5">
			<div class="inner">
				<?php
					$settings = getSettings();

					if ($displaySuccess) {
						include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
					}
				?>
				<h2>Basic Settings</h2>
				<form class="pure-form pure-form-aligned" action="./" method="POST">
					<input type="hidden" name="google_api_key" value="AIzaSyBW9_MzcUZOaOkXEk57fkZtS-0K3b4-24s">
					<input type="hidden" name="gosugamers_api_key" value="190ef35b547947d9c5420dc5292f1fb6">
					<input type="hidden" name="steam_api_key" value="C7CB039CE855C92517AA7F7D42C43358">
					<fieldset>
						<div class="pure-control-group">
							<label for="target_subreddit">Target Subreddit</label>
							<input type="text" name="target_subreddit" value="<?php echo $settings['target_subreddit']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Update Timeout (minutes)</label>
							<input type="text" name="update_timeout" value="<?php echo $settings['update_timeout']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Maximum Streams</label>
							<input type="text" name="max_streams_shown" value="<?php echo $settings['max_streams_shown']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Maximum Games</label>
							<input type="text" name="max_games_shown" value="<?php echo $settings['max_games_shown']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Stream Thumbnail CSS Name</label>
							<input type="text" name="stream_thumbnail_css_name" value="<?php echo $settings['stream_thumbnail_css_name']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Spotlight Rotation (minutes)</label>
							<input type="text" name="spotlight_rotation_timeout" value="<?php echo $settings['spotlight_rotation_timeout']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Number of Headers</label>
							<input type="text" name="num_of_headers" value="<?php echo $settings['num_of_headers']; ?>">
						</div>
						<div class="pure-control-group">
							<label>Minify stylesheet?</label>
							<input type="checkbox" name="minify_stylesheet"<?php echo ($settings['minify_stylesheet'] == true ? " checked" : ""); ?>>
						</div>
						<div class="pure-controls">
							<button type="submit" class="pure-button pure-button-primary">Submit</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>