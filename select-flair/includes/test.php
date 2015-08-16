<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/database.php");

$database = new database();

$ret = $database->getUser("GlobalOffensiveBot")['current_access_token'];

var_dump($ret);

?>