<?php

if (!isset($_SESSION)) { session_start(); }

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/steam/api.php";

$_SESSION['inventory'] = json_decode(getUserInventory($_SESSION['steamId']), true);

$_SESSION['inventory'] = array_merge($_SESSION['inventory'], ['reddit_username' => $_SESSION['redditUsername']]);

echo json_encode($_SESSION['inventory'], JSON_NUMERIC_CHECK);

?>