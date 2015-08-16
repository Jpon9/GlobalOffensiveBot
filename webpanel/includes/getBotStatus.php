<?php
	function getBotStatus() {
		$status = "indeterminate.";
		if (PHP_OS == 'WINNT') {
			$result = [];
			exec("tasklist /FI \"IMAGENAME eq python.exe\" /FO TABLE", $result);
			return ["status" => count($result) > 1 ? "online" : "offline"];
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
			return ["status" => isset($matches[2]) ? "online" : "offline"];
		}
	}

	if (isset($_GET['verbose'])) {
		echo json_encode(getBotStatus());
	}
?>