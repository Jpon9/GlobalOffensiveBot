<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$winnersPath = $giveaway_bot_path . "cache/winners.json";

	function getWinners() {
		global $winnersPath;
		return json_decode(file_get_contents($winnersPath), true);
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getWinners(), JSON_NUMERIC_CHECK);
	}
?>