<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/settings.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/lastWebpanelRestart.php";

	$mainPy = "main.py";
	$bot_mainpy = $bot_path . $mainPy;

	$python = "";
	if (PHP_OS == 'WINNT') {
		$python = "python.exe";
	} else {
		$python = "python";
	}

	function dump($s) {
		echo "<pre>" . print_r($s, true) . "</pre>";
	}

	// Found here: https://php.net/manual/en/function.exec.php#86329
	function execInBackground($cmd) {
		if (PHP_OS == 'WINNT') {
		    $WshShell = new COM("WScript.Shell");
			$oExec = $WshShell->Run($cmd, 0, false);
		} else {
			var_dump('wfad');
			exec("$cmd > /tmp/restartbot.log 2>&1 &");
		}
	}

	function getProcessId() {
		global $python;
		global $mainPy;
		if (PHP_OS == 'WINNT') {
			$result = [];
			exec("TASKLIST /FI \"IMAGENAME eq $python\" /FO TABLE", $result);
			if (substr($result[0], 0, strlen("INFO")) == "INFO") {
				return false;
			} else {
				$result = array_slice($result, 3);
			}
			foreach ($result as $line) {
				$line = explode(' ', preg_replace('/\s+/', ' ', $line));
				if ($line[0] == $python) {
					return $line[1];
				}
			}
		} else {
			$result = explode("\n", shell_exec("ps -ef | grep '$python.*$mainPy' -"));
			foreach ($result as $line) {
				$line = explode(' ', preg_replace('/\s+/', ' ', $line));
				if (($line[7] == $python || $line[7] == "/usr/bin/$python") && ($mainPy == "" || $line[8] == $mainPy)) {
					return $line[1];
				}
			}
		}
		return false;
	}

	function killProcess() {
		$pid = getProcessId();
		if ($pid == false) { return false; } 
		if (PHP_OS == 'WINNT') {
			exec("TASKKILL /F /pid " . $pid);
		} else {
			exec("kill -15 " . $pid);
		}
		if (getProcessId() != false) {
			return false;
		}
		return true;
	}

	function restartBot() {
		global $python;
		global $bot_mainpy;

		var_dump(killProcess());

		execInBackground("\"$bot_mainpy\"");

		if (getProcessId($python)) {
			return ["status" => "success"];
		}
		return ["status" => "failure"];
	}

	if (isset($_GET['verbose']) && $_GET['verbose'] == 'restartBot') {
		$status = restartBot();
		if ($status['status'] == 'success') {
			setLastWebpanelRestart(time());
		}
		echo json_encode($status);
	}

	if (isset($_GET['test1'])) {
		var_dump(killProcess());
	}

	if (isset($_GET['test2'])) {
		var_dump(execInBackground("\"$bot_mainpy\""));
	}

	if (isset($_GET['test3'])) {
		var_dump(getProcessId());
	}

	if (isset($_GET['whoami'])) {
		var_dump(exec('whoami'));
	}

	if (isset($_GET['pwd'])) {
		var_dump(exec('pwd'));
	}
?>