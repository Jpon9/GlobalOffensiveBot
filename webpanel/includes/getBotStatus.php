<?php
	function getBotStatus() {
		$status = "indeterminate.";
		if (PHP_OS == 'WINNT') {
			$result = [];
			exec("tasklist /FI \"IMAGENAME eq python.exe\" /FO TABLE", $result);
			if (count($result) <= 1) {
				return "offline";
			}
			return "online";
		} else {
			$processStatus = explode("\n", shell_exec('ps -ef | grep python -'));
			$psLine = "";
			foreach ($processStatus as $ps) {
				if (strpos($ps, "main.py") !== false) {
					$psLine = $ps;
					break;
				}
			}
			$matches = [];
			preg_match("/(jpon9|www-data) +(\d+) /", $psLine, $matches);
			return isset($matches[2]) ? "online" : "offline";
		}
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(["status" => getBotStatus()]);
	}
?>