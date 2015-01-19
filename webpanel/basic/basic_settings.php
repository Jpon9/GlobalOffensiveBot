<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$settingsPath = $bot_path . "config/settings.json";

	function getSettings() {
		global $settingsPath;
		return json_decode(file_get_contents($settingsPath), true)["settings"];
	}

	function updateSettings($targetSubreddit, $updateTimeout, $maxStreams, $maxGames, $thumbnail, $spotlightTimeout, $google, $gosu, $steam) {
		global $settingsPath;
		$settings = [
			"settings" => [
				"target_subreddit" => $targetSubreddit,
				"update_timeout" => $updateTimeout,
				"max_streams_shown" => $maxStreams,
				"max_games_shown" => $maxGames,
				"stream_thumbnail_css_name" => $thumbnail,
				"spotlight_rotation_timeout" => $spotlightTimeout,
				"google_api_key" => $google,
				"gosugamers_api_key" => $gosu,
				"steam_api_key" => $steam
			]
		];
		$output = fopen($settingsPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, json_encode($settings, JSON_NUMERIC_CHECK));
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Basic settings updated.");
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getSettings(), JSON_NUMERIC_CHECK);
	}
?>