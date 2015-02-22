<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/botRootDir.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$commentersPath = $_SERVER['DOCUMENT_ROOT'] . "/top-commenters/commenters.json";

	function getCommenters() {
		global $commentersPath;
		return json_decode(file_get_contents($commentersPath), true);
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getCommenters(), JSON_NUMERIC_CHECK);
	}
?>