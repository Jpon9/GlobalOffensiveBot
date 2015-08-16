<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/getBotMetadata.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$metadataPath = $bot_path . "cache/metadata.json";

	function getLastWebpanelRestart() {
		global $metadataPath;
		return json_decode(file_get_contents($metadataPath), true)['last_webpanel_restart'];
	}

	function setLastWebpanelRestart($lastWebpanelRestart) {
		global $metadataPath;
		$metadata = getMetadata();
		$metadata['last_webpanel_restart'] = $lastWebpanelRestart;
		$output = fopen($metadataPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, json_encode($metadata, JSON_NUMERIC_CHECK));
		flock($output, LOCK_UN);
		fclose($output);
	}

	if (isset($_GET['verbose']) && $_GET['verbose'] == 'lastWebpanelRestart') {
		echo json_encode(getLastWebpanelRestart(), JSON_NUMERIC_CHECK);
	}
?>