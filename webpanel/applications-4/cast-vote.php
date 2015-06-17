<?php

if (isset($_GET['user']) && isset($_GET['verdict'])) {
	castVote($_GET['user'], $_GET['verdict']);
}

function castVote($user, $verdict) {
	$voter = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "unknown";

	$filepath = "./votes/{$voter}.json";
	$vote = [$user => $verdict];
	if (!file_exists($filepath)) {
		$file = fopen($filepath, "w");
		flock($file, LOCK_EX);
		fwrite($file, json_encode($vote, JSON_NUMERIC_CHECK));
		flock($file, LOCK_UN);
		fclose($file);
	} else {
		$voteHistory = json_decode(file_get_contents($filepath), true);
		$isDuplicate = false;
		foreach ($voteHistory as $i => $v) {
			if ($i == $user) {
				$voteHistory[$user] = $vote[$user];
				$isDuplicate = true;
			}
		}
		if (!$isDuplicate) {
			$voteHistory = array_merge($voteHistory, $vote);
		}
		$file = fopen($filepath, "w");
		flock($file, LOCK_EX);
		fwrite($file, json_encode($voteHistory, JSON_NUMERIC_CHECK));
		flock($file, LOCK_UN);
		fclose($file);
	}
}
	
	
?>