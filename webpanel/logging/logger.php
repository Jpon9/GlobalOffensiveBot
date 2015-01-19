<?php
	$logPath = $_SERVER['DOCUMENT_ROOT'] . "/logging/log.txt";
	$logPathJson = $_SERVER['DOCUMENT_ROOT'] . "/logging/log.json";

	function logAction($msg) {
		global $logPath;
		$user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "unknown";
		date_default_timezone_set("EST");
		$time = date("F jS, Y @ H:i:s T");
		$message = "[" . $time . "] [" . $user . "] " . $msg . "\n";
		$output = fopen($logPath, 'a');
		flock($output, LOCK_EX);
		fwrite($output, $message);
		flock($output, LOCK_UN);
		fclose($output);
	}

	function getLog() {
		global $logPathJson;
		return file_get_contents($logPathJson);
	}

	if (isset($_GET['verbose_log'])) {
		echo getLog();
	}
?>