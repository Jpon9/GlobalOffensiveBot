<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$sidebarPath = $bot_path . "config/description.json";

	function getSidebar() {
		global $sidebarPath;
		return json_decode(file_get_contents($sidebarPath), true);
	}

	function updateSidebar($sidebar) {
		global $sidebarPath;
		$newSidebar = ['template'=>$_POST['template'],'chunks'=>[]];
		foreach ($sidebar['chunks'] as $chunk) {
			array_push($newSidebar['chunks'], array("name" => $chunk['name'], "body" => $_POST[$chunk['name']]));
		}
		$output = fopen($sidebarPath, 'w');
		flock($output, LOCK_EX);
		fwrite($output, json_encode($newSidebar, JSON_NUMERIC_CHECK));
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Sidebar updated.");
	}

	// Lets Javascript call this function by AJAX'ing this file
	if (isset($_GET['verbose'])) {
		echo json_encode(getSidebar(), JSON_NUMERIC_CHECK);
	}
?>