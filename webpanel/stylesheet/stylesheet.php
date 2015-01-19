<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$stylesheetPath = $bot_path . "/config/stylesheet/main_stylesheet.txt";

	function getStylesheet() {
		global $stylesheetPath;
		return file_get_contents($stylesheetPath);
	}

	function updateStylesheet($stylesheet) {
		global $stylesheetPath;
		$output = fopen($stylesheetPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, $stylesheet);
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Stylesheet updated.");
	}
?>