<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/logging/logger.php";
	$noticesPath = $bot_path . "/config/notices.json";
	//$noticesPath = $_SERVER['DOCUMENT_ROOT'] . "/notices/cache/notices.json";
	//$noticesPath = $_SERVER['DOCUMENT_ROOT'] . "/notices/stickies.json";

	error_reporting(~0); ini_set('display_errors', 1);

	if (isset($_POST['notices'])) {
		$notices = array("notices" => json_decode($_POST['notices'], true));
		updateNotices($notices);
	}

	function getNotices() {
		global $noticesPath;
		return json_decode(file_get_contents($noticesPath), true);
	}

	function updateNotices($notices) {
		global $noticesPath;
		$oldNotices = getNotices();
		$output = fopen($noticesPath, 'w');
		flock($output, LOCK_EX);
		// END DEBUG
		fwrite($output, json_encode(["notices" => mergeNotices($oldNotices['notices'], $notices['notices'])], JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT));
		flock($output, LOCK_UN);
		fclose($output);
		logAction("Notices updated.");
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getNotices(), JSON_NUMERIC_CHECK);
	}

	// Updates the current notices on the server with the new values from the webpanel.
	function mergeNotices($a, $b) {
		// Layer in the diffs from the webpanel
		foreach ($b as $bo) {
			$matchFound = false;
			foreach ($a as $ai => $av) {
				if ($a[$ai]['unique_notice_id'] == $bo['unique_notice_id']) {
					$matchFound = true;
					foreach ($bo as $bp => $bv) {
						$a[$ai][$bp] = $bv;
						echo "'" . $bp . "' set to '" . $bv . "'\n";
					}
				}
			}
			if (!$matchFound) { array_push($a, $bo); }
		}
		// Make sure anything removed on the webpanel is removed in the bot
		foreach ($a as $ai => $av) {
			$found = false;
			foreach ($b as $bo) {
				if ($a[$ai]['unique_notice_id'] == $bo['unique_notice_id']) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				unset($a[$ai]);
			}
		}
		return array_values($a);
	}
?>