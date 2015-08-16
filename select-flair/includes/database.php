<?php

class database {
	private $database;

	public function __construct($host = "globaloffensivebot.com",
							   $uname = "webpanel",
							   $pword = "wCFiO7mEeYsxwBdkSfAX",
							   $db = "globaloffensivebot") {
		$this->database = new mysqli($host, $uname, $pword, $db);

		if ($this->database->connect_error) {
			die("Database connection failed: " . $this->database->connect_error);
		}
	}

	public function updateUser($user, $accessToken, $refreshToken, $expires, $scope) {
		if (!self::isReturningUser($user)) {
			$success = $this->database->query("INSERT INTO webpanel (user, current_access_token, refresh_token, access_token_expires, scope) VALUES ('{$user}', '{$accessToken}', '{$refreshToken}', {$expires}, '{$scope}')");
			if (!$success) {
				die("Could not insert user credentials.");
			}
		} else {
			$success = $this->database->query("UPDATE webpanel SET current_access_token='{$accessToken}', access_token_expires={$expires}, scope='{$scope}' WHERE user='{$user}'");
			if (!$success) {
				die("Could not update user credentials.");
			}
		}
	}

	public function getRefreshToken($accessToken) {
		$result = $this->database->query("SELECT refresh_token FROM webpanel WHERE current_access_token='{$accessToken}'");
		$result = $result->fetch_array(MYSQLI_ASSOC);
		if (isset($result['refresh_token'])) {
			return $result['refresh_token'];
		} else {
			die("COUDLN'T GET DA REFRESH TOKEN MON");
			return false;
		}
	}

	public function isTokenExpired($accessToken) {
		$result = $this->database->query("SELECT access_token_expires FROM webpanel WHERE current_access_token='{$accessToken}'");
		$result = $result->fetch_array(MYSQLI_ASSOC);
		if (isset($result['access_token_expires'])) {
			return $result['access_token_expires'] < time();
		} else {
			return null;
		}
	}

	public function dropUser($accessToken) {
		$refreshToken = self::getRefreshToken($accessToken);
		$this->database->query("DELETE FROM webpanel WHERE current_access_token='{$accessToken}'");
		return $refreshToken;
	}

	public function getUser($user) {
		$result = $this->database->query("SELECT * FROM webpanel WHERE user='{$user}'");
		$result = $result->fetch_array(MYSQLI_ASSOC);
		if (isset($result)) {
			return $result;
		} else {
			return null;
		}
	}

	private function isReturningUser($user) {
		$results = $this->database->query("SELECT user FROM webpanel WHERE user='{$user}'");
		if ($results == NULL || $results->num_rows == 0) {
			return false;
		} else {
			return true;
		}
	}
}

if (!isset($_SESSION)) { session_start(); }

?>