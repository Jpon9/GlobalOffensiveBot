<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$demonymsPath = $bot_path . "config/stylesheet/random_demonyms.json";

	function getDemonyms() {
		global $demonymsPath;
		return json_decode(file_get_contents($demonymsPath), true)['demonyms'];
	}

	function updateDemonyms($demonyms) {
		global $demonymsPath;
		$output = fopen($demonymsPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, json_encode($demonyms, JSON_NUMERIC_CHECK));
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Demonyms updated.");
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getDemonyms(), JSON_NUMERIC_CHECK);
	}
?>