<?php
	$apiKey = "A32E900C492EDF62C8656BC110FF0E4C";

	// Takes core parts of API calls and constructs one based on those parameters
	// Also adds appropriate information into the database
	function apiCall($directory, $version, $params, $offline = "") {
		global $apiKey;
		$parameters = "";
	    $isApiCall = false; // Does the call involve our API key?
		if ($params != NULL) {
			$parameters = "/?";
			foreach ($params as $key => $value) {
				if ($value == "#key") { $value = $apiKey; $isApiCall = true; }
				$parameters .= $key . "=" . $value . "&";
			}
		}
		$ch = curl_init();
		if ($isApiCall === true) { $_SESSION["api_calls"] += 1; }
		curl_setopt($ch, CURLOPT_URL, "http://api.steampowered.com/" . $directory . "/" . $version . $parameters);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$url = curl_exec($ch);
		if ($url == false) {
			return NULL;
		}
		curl_close($ch);
		return $url;
	}

	function getUserInventory($steamId) {
		return file_get_contents("cache.json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://steamcommunity.com/profiles/{$steamId}/inventory/json/730/2");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$url = curl_exec($ch);
		if ($url == false) {
			return NULL;
		}
		curl_close($ch);
		return $url;
	}
?>