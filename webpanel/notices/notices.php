<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$noticesPath = $bot_path . "/config/notices.json";
	//$noticesPath = $_SERVER['DOCUMENT_ROOT'] . "/notices/cache/notices.json";
	//$noticesPath = $_SERVER['DOCUMENT_ROOT'] . "/notices/stickies.json";

	if (isset($_POST['notices'])) {
		updateNotices(array("notices" => json_decode($_POST['notices'])));
	}

	function getNotices() {
		global $noticesPath;
		return json_decode(file_get_contents($noticesPath), true);
	}

	function updateNotices($notices) {
		global $noticesPath;
		$output = fopen($noticesPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, json_encode($notices, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Notices updated.");
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getNotices(), JSON_NUMERIC_CHECK);
	}
?>