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
	$allVotes[$i] = json_decode(file_get_contents("./votes/{$filepaths[$i]}"));
}

foreach ($allVotes as $votes) {
	foreach ($votes as $user => $verdict) {
		if (!isset($tallies[$user])) {
			$tallies[$user] = [];
			$tallies[$user]['approve'] = 0;
			$tallies[$user]['neutral'] = 0;
			$tallies[$user]['deny'] = 0;
		}
		$tallies[$user][$verdict]++;
	}
}

echo json_encode($tallies, JSON_NUMERIC_CHECK);

?>