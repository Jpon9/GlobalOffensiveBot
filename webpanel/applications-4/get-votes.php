<?php

$voter = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "unknown";
$filepath = "./votes/{$voter}.json";
echo file_get_contents($filepath);

?>