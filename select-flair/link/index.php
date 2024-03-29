<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/reddit/api.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/openid.php');
    include_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    if (!isset($_SESSION)) { session_start(); }

    if (!isset($_SESSION['redditUsername'])) {
    	unset($_SESSION['reddit']);
    	new reddit();
	    $_SESSION["redditUsername"] = $_SESSION['reddit']->getUser()->name;
    }

    if (!isset($_SESSION['steamId'])) {
	    $openid = new LightOpenId($domain);

	    if (!$openid->mode) {
	        $openid->identity = "http://steamcommunity.com/openid";
	        header("Location: " . $openid->authUrl());
	    } else if ($openid->mode == "cancel") {
	        //echo "User cancelled Steam auth";
	    } else {
	        if (!isset($_SESSION["steamAuth"])) {
	            $_SESSION["steamAuth"] = $openid->validate() ? $openid->identity : null;

	            header("Location: http://" . $domain);
	        }
	        $_SESSION["steamId"] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION["steamAuth"]);
	    }
    }

    var_dump($_SESSION['steamId']);
    var_dump($_SESSION['redditUsername']);

    die("wat");

    header("Location: http://" . $domain);
?>