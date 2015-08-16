<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/reddit2/api.php');

    session_destroy();

    if (!isset($_SESSION)) { session_start(); }

    new reddit2();

    $authStatus = "succeeded!";
    $authUsername = $_SESSION['reddit']->getUser()->name;

    if (!isset($authUsername)) {
    	$authStatus = "failed!";
    }
?>

<html>
	<head>
		<title>GlobalOffensiveBot Auth</title>
	</head>
	<body>
		<h2><?php echo $authUsername; ?> auth <?php echo $authStatus; ?></h2>
	</body>
</html>