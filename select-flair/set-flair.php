<?php

if (!isset($_SESSION)) { session_start(); }

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/reddit2/api.php";

function setFlair() {
	if (!isset($_SESSION['inventory'])) {
		return '{"status":"no inventory"}';
	} else if (!isset($_GET['flair'])) {
		return '{"status":"no flair"}';
	}

	$flairMapping = [
		/* Operation Coins */
        "op_paybackbronze" => "Bronze Operation Payback Coin",
        "op_paybacksilver" => "Silver Operation Payback Coin",
        "op_paybackgold" => "Gold Operation Payback Coin",
        "op_bravobronze" => "Bronze Operation Bravo Coin",
        "op_bravosilver" => "Silver Operation Bravo Coin",
        "op_bravogold" => "Gold Operation Bravo Coin",
        "op_phoenixbronze" => "Bronze Operation Phoenix Coin",
        "op_phoenixsilver" => "Silver Operation Phoenix Coin",
        "op_phoenixgold" => "Gold Operation Phoenix Coin",
        "op_breakoutbronze" => "Bronze Operation Breakout Coin",
        "op_breakoutsilver" => "Silver Operation Breakout Coin",
        "op_breakoutgold" => "Gold Operation Breakout Coin",
        "op_vanguardbronze" => "Bronze Operation Vanguard Coin",
        "op_vanguardsilver" => "Silver Operation Vanguard Coin",
        "op_vanguardgold" => "Gold Operation Vanguard Coin",
        /* Pick'Em Challenge Coins */
        "pe_eslonecologne2014bronze" => "Bronze ESL One Cologne 2014 Pick'Em Challenge Trophy",
        "pe_eslonecologne2014silver" => "Silver ESL One Cologne 2014 Pick'Em Challenge Trophy",
        "pe_eslonecologne2014gold" => "Gold ESL One Cologne 2014 Pick'Em Challenge Trophy",
        "pe_dreamhackwinter2014bronze" => "Bronze DreamHack Winter 2014 Pick'Em Challenge Trophy",
        "pe_dreamhackwinter2014silver" => "Silver DreamHack Winter 2014 Pick'Em Challenge Trophy",
        "pe_dreamhackwinter2014gold" => "Gold DreamHack Winter 2014 Pick'Em Challenge Trophy",
        "pe_eslonekatowice2015bronze" => "Bronze ESL One Katowice 2015 Pick'Em Challenge Trophy",
        "pe_eslonekatowice2015silver" => "Silver ESL One Katowice 2015 Pick'Em Challenge Trophy",
        "pe_eslonekatowice2015gold" => "Gold ESL One Katowice 2015 Pick'Em Challenge Trophy",
        /* Map Author Coins */
        "mc_museum" => "Prototype Museum Map Coin",
        "mc_downtown" => "Prototype Downtown Map Coin",
        "mc_thunder" => "Prototype Thunder Map Coin",
        "mc_favela" => "Prototype Favela Map Coin",
        "mc_motel" => "Prototype Motel Map Coin",
        "mc_seaside" => "Prototype Seaside Map Coin",
        "mc_library" => "Prototype Library Map Coin",
        "mc_agency" => "Prototype Agency Map Coin",
        "mc_ali" => "Prototype Ali Map Coin",
        "mc_cache" => "Prototype Cache Map Coin",
        "mc_chinatown" => "Prototype Chinatown Map Coin",
        "mc_gwalior" => "Prototype Gwalior Map Coin",
        "mc_ruins" => "Prototype Ruins Map Coin",
        "mc_siege" => "Prototype Siege Map Coin",
        "mc_castle" => "Prototype Castle Map Coin",
        "mc_blackgold" => "Prototype Black Gold Map Coin",
        "mc_rush" => "Prototype Rush Map Coin",
        "mc_mist" => "Prototype Mist Map Coin",
        "mc_insertion" => "Prototype Insertion Map Coin",
        "mc_overgrown" => "Prototype Overgrown Map Coin",
        "mc_marquis" => "Prototype Marquis Map Coin",
        "mc_workout" => "Prototype Workout Map Coin",
        "mc_backalley" => "Prototype Backalley Map Coin",
        "mc_season" => "Prototype Season Map Coin",
        "mc_bazaar" => "Prototype Bazaar Map Coin",
        "mc_facade" => "Prototype Facade Map Coin",
        "mc_log" => "Prototype Log Map Coin",
        "mc_rails" => "Prototype Rails Map Coin",
        "mc_resort" => "Prototype Resort Map Coin",
        "mc_zoo" => "Prototype Zoo Map Coin",
        /* Exotic Pins */
        "mp_dustii" => "Genuine Dust II Pin",
        "mp_guardianelite" => "Genuine Guardian Elite Pin",
        "mp_mirage" => "Genuine Mirage Pin",
        "mp_inferno" => "Genuine Inferno Pin",
        "mp_italy" => "Genuine Italy Pin",
        "mp_victory" => "Genuine Victory Pin",
        "mp_militia" => "Genuine Militia Pin",
        "mp_nuke" => "Genuine Nuke Pin",
        "mp_guardian" => "Genuine Guardian Pin",
        "mp_tactics" => "Genuine Tactics Pin",
        "mp_train" => "Genuine Train Pin",
        /* Showcase Items */
        "sc_5yearcoin" => "5 Year Veteran Coin"
	];

	if (!isset($flairMapping[$_GET['flair']])) {
		return '{"status":"invalid flair (1)"}';
	}

	$flairValid = false;
	foreach ($_SESSION['inventory']['rgDescriptions'] as $item) {
    	if ($item['name'] == $flairMapping[$_GET['flair']]) {
    		$flairValid = true;
    		break;
    	}
	}

	if (!$flairValid) {
		return '{"status":"invalid flair (2)"}';
	}

	// make Reddit API call to set flair

	$reddit2 = new reddit2();
	$r = $reddit2->setFlair("GlobalOffensiveTest", $_SESSION['redditUsername'], $flairMapping[$_GET['flair']], "special " . str_replace("_", "-", $_GET['flair']));
    echo "<pre>" . print_r($r, true) . "</pre>";

	return '{"status":"success"}';
}

echo setFlair();

?>