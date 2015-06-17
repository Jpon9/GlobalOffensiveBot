<?php

$filepaths_raw = scandir("./votes/");
$filepaths = [];
$allVotes = [];
$tallies = [];

foreach ($filepaths_raw as $filepath) {
	if ($filepath == "." || $filepath == "..") {
		continue;
	}

	array_push($filepaths, $filepath);
}

for ($i = 0; $i < count($filepaths); $i++) {
	$allVotes[str_replace(".json", "", $filepaths[$i])] = json_decode(file_get_contents("./votes/{$filepaths[$i]}"));
}

foreach ($allVotes as $voter => $votes) {
	foreach ($votes as $user => $verdict) {
		if (!isset($tallies[$voter])) {
			$tallies[$voter] = [];
			$tallies[$voter]['approve'] = 0;
			$tallies[$voter]['neutral'] = 0;
			$tallies[$voter]['deny'] = 0;
			$tallies[$voter]['total'] = 0;
		}
		$tallies[$voter][$verdict]++;
		$tallies[$voter]["total"]++;
	}
}

echo json_encode($tallies, JSON_NUMERIC_CHECK);

?>