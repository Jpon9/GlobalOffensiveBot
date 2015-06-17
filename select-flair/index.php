<?php
    if (!isset($_SESSION)) { session_start(); }

    include_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    if (!isset($_SESSION["steamId"]) || !isset($_SESSION["redditUsername"])) {
    	header("Location: http://{$domain}/intro");
    }
?>

<?php
	$title = "/r/GlobalOffensive Flair Selection";
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/flair-selection.css">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/loading.js"></script>
		<script type="text/javascript" src="/js/inventory.js"></script>
	</head>
	<body class="pure-g">
		<div class="loading">
			<div id="stats-holder">
                <p class="loading">Loading</p>
                <div id="loadAnim">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <p class="loading">You&#39;ve been waiting for <span id="loadingTimer">0.000 s</span></p>
                <p class="loading"><em id="loadingDesc">And that&#39;s not a long time!</em></p>
            </div>
            <div id="result-status">
                <p id="status"></p>
            </div>
        </div>
        <div id="inventory">
            <h1>Hello, <span id="reddit-username">N/A</span>!</h1>
        </div>
        <script type="text/javascript">
        	var target = document.getElementById("inventory");
            var intervalId = loading(function () {if(xhr!==null){xhr.abort();}});
            var xhr = $.post("./get-inventory.php", function(data) {
                clearInterval(intervalId);
                var exception = false;
                var fullReturn = null;
                var inventory = null;
                try {
                    fullReturn = JSON.parse(data);
                    inventory = fullReturn['rgDescriptions'];
                } catch (e) {
                    exception = true;
                    target.innerHTML = data;
                }
                if (!exception) {
                    document.getElementById("reddit-username").innerHTML = fullReturn['reddit_username'];

                    var flairs = generateOptions(target, inventory);

                    // Small helper to keep things clean
                    function createElem(e, p) {
                        var elem = document.createElement(e);
                        p.appendChild(elem);
                        return elem;
                    }

                    var op = createElem("DIV", target);
                    var pe = createElem("DIV", target);
                    var mc = createElem("DIV", target);
                    var mp = createElem("DIV", target);
                    op.className = "flair-group";
                    pe.className = "flair-group";
                    mc.className = "flair-group";
                    mp.className = "flair-group";

                    createElem("H2", op).innerHTML = "Operation Coin Flairs";
                    createElem("H2", pe).innerHTML = "Pick'Em Challenge Flairs";
                    createElem("H2", mc).innerHTML = "Map Coin Flairs";
                    createElem("H2", mp).innerHTML = "Exotic Pin Flairs";

                    function setFlair(ev) {
                        var flair = ev.target.attributes[0].nodeValue;
                        if (flair.search("images") !== -1) {
                            flair = flair.split('/')[2].split('.')[0];
                        }
                        var intervalId = loading(function () {if(xhr2!==null){xhr2.abort();}});
                        $("div.loading").fadeIn(100);
                        var xhr2 = $.getJSON("./set-flair.php?flair=" + flair, function(data) {
                            document.getElementById("stats-holder").style.display = "none";
                            document.getElementById("result-status").style.display = "block";
                            document.getElementById("status").innerHTML = data.status === "success" ? "Success! Your flair will be live within a few minutes." : "Error! Something went wrong:<br>" + data.status;
                            setTimeout(function() {
                                $("div.loading").fadeOut(350);
                            }, 3000);
                        });
                    }

                    for (var i in flairs) {
                        if (flairs[i]['status'] === false) { continue; }
                        var identifier = i.substr(0, 2);
                        var parent = op;
                        if (identifier === "pe") {
                            parent = pe;
                        } else if (identifier === "mc") {
                            parent = mc;
                        } else if (identifier === "mp") {
                            parent = mp;                          
                        }
                        var flairButton = createElem("A", parent);
                        flairButton.innerHTML = flairs[i]['name'];
                        flairButton.setAttribute('data-flair-name', i + (flairs[i].status !== false ? flairs[i].status : ""));
                        var flairImage = createElem("IMG", flairButton);
                        flairImage.src = "./images/" + i + (flairs[i].status !== false ? flairs[i].status : "") + ".png";
                        flairImage.alt = flairs[i]['name'] + " Image";
                        $(flairButton).click(setFlair);
                    }

                    if ($(op).children().size() === 1) {
                        op.style.display = "none";
                    }
                    if ($(pe).children().size() === 1) {
                        pe.style.display = "none";
                    }
                    if ($(mc).children().size() === 1) {
                        mc.style.display = "none";
                    }
                    if ($(mp).children().size() === 1) {
                        mp.style.display = "none";
                    }

                    $("div.loading").fadeOut(550, function() {
                        $(target).fadeIn(550);
                    });
                }
            });

            /*
             *  Generate the flair options
             */
            function generateOptions(target, inventory) {
                var flairOptions = {};
//                  var flairOptions = {
//                      /* Operation Coins */
//                      op_payback: false,
//                      op_bravo: false,
//                      op_phoenix: false,
//                      op_breakout: false,
//                      op_vanguard: false,
//                      /* Pick'Em Challenge Coins */
//                      pe_eslonecologne2014: false,
//                      pe_dreamhackwinter2014: false,
//                      pe_eslonekatowice2015: false,
//                      /* Map Author Coins */
//                      mc_museum: false,
//                      mc_downtown: false,
//                      mc_thunder: false,
//                      mc_favela: false,
//                      mc_motel: false,
//                      mc_seaside: false,
//                      mc_library: false,
//                      mc_agency: false,
//                      mc_ali: false,
//                      mc_cache: false,
//                      mc_chinatown: false,
//                      mc_gwalior: false,
//                      mc_ruins: false,
//                      mc_siege: false,
//                      mc_castle: false,
//                      mc_blackgold: false,
//                      mc_rush: false,
//                      mc_mist: false,
//                      mc_insertion: false,
//                      mc_overgrown: false,
//                      mc_marquis: false,
//                      mc_workout: false,
//                      mc_backalley: false,
//                      mc_season: false,
//                      mc_bazaar: false,
//                      mc_facade: false,
//                      mc_log: false,
//                      mc_rails: false,
//                      mc_resort: false,
//                      mc_zoo: false,
//                      /* Exotic Pins */
//                      mp_dustii: false,
//                      mp_guardianelite: false,
//                      mp_mirage: false,
//                      mp_inferno: false,
//                      mp_italy: false,
//                      mp_victory: false,
//                      mp_militia: false,
//                      mp_nuke: false,
//                      mp_guardian: false,
//                      mp_tactics: false,
//                      mp_train: false
//                  };
                for (var i in inventory) {
                    /* Operation Coins */
                    var operation = inventory[i].name.match(/Operation ([a-zA-Z]{3,32}) Coin/);
                    if (operation !== null) {
                        flairOptions["op_" + operation[1].toLowerCase()] = {};
                        flairOptions["op_" + operation[1].toLowerCase()]['status'] = inventory[i].name.substr(0, inventory[i].name.indexOf(' ')).toLowerCase();
                        flairOptions["op_" + operation[1].toLowerCase()]['name'] = operation[1] + " Coin";
                        flairOptions["op_" + operation[1].toLowerCase()]['fullname'] = inventory[i].name;
                        continue;
                    }
                    /* Pick'Em Challenge Coins */
                    var pickem = inventory[i].name.match(/((ESL One (Katowice|Cologne)|DreamHack Winter) 201\d) Pick'Em Challenge Trophy/);
                    if (pickem !== null) {
                        flairOptions["pe_" + pickem[1].toLowerCase().split(' ').join('')] = {};
                        flairOptions["pe_" + pickem[1].toLowerCase().split(' ').join('')]['status'] = inventory[i].name.substr(0, inventory[i].name.indexOf(' ')).toLowerCase();
                        flairOptions["pe_" + pickem[1].toLowerCase().split(' ').join('')]['name'] = pickem[1];
                        flairOptions["pe_" + pickem[1].toLowerCase().split(' ').join('')]['fullname'] = inventory[i].name;
                        continue;
                    }
                    /* Map Author Coins */
                    var mapauthor = inventory[i].name.match(/Prototype (.{3,32}) Map Coin/);
                    if (mapauthor !== null) {
                        flairOptions["mc_" + mapauthor[1].toLowerCase().split(' ').join('')] = {};
                        flairOptions["mc_" + mapauthor[1].toLowerCase().split(' ').join('')]['status'] = true;
                        flairOptions["mc_" + mapauthor[1].toLowerCase().split(' ').join('')]['name'] = inventory[i].name;
                        flairOptions["mc_" + mapauthor[1].toLowerCase().split(' ').join('')]['fullname'] = inventory[i].name;
                        continue;
                    }
                    /* Exotic Pins */
                    var pin = inventory[i].name.match(/Genuine (.{3,32}) Pin/);
                    if (pin !== null) {
                        flairOptions["mp_" + pin[1].toLowerCase().split(' ').join('')] = {};
                        flairOptions["mp_" + pin[1].toLowerCase().split(' ').join('')]['status'] = true;
                        flairOptions["mp_" + pin[1].toLowerCase().split(' ').join('')]['name'] = inventory[i].name;
                        flairOptions["mp_" + pin[1].toLowerCase().split(' ').join('')]['fullname'] = inventory[i].name;
                        continue;
                    }
                }
                return flairOptions;
            }
        </script>
	</body>
</html>