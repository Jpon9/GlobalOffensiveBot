<?php
	$bot_path = "";
	$giveaway_bot_path = "";

	if (PHP_OS == "WINNT") {
		$bot_path = "C:/path/to/bot/";
		$giveaway_bot_path = "C:/path/to/giveaway/bot/";
	} else {
		$bot_path = "/var/www/globaloffensivebot/bot/";
		$giveaway_bot_path = "/var/www/globaloffensivebot/giveaway_bot/";
	}
?>