<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	$metadataPath = $bot_path . "/cache/metadata.json";

	function getMetadata() {
		global $metadataPath;
		return json_decode(file_get_contents($metadataPath), true);
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getMetadata(), JSON_NUMERIC_CHECK);
	}
?>