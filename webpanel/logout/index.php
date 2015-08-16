<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/reddit/api.php');
    
	if (!isset($_SESSION)) { session_start(); }
    
    if (!isset($_SESSION['reddit'])) {
        new reddit();
    }
    $reddit = $_SESSION['reddit'];

    $reddit->logout();
?>