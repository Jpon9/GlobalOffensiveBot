<?php
	include_once("./demonyms.php");

	$title = "Demonyms";
	$displaySuccess = false;

	if (isset($_POST["demonym0"])) {
		$i = 0;
		$demonyms = ["demonyms" => []];
		foreach ($_POST as $key => $value) {
			if (preg_match("/^demonym\d+$/", $key) == 1) {
				array_push($demonyms['demonyms'], ["subscribers"=>$value[0],"online"=>$value[1]]);
			}
		}
		updateDemonyms($demonyms);
		$displaySuccess = true;
		unset($_POST);
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/demonyms.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body class="pure-g">
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="main" class="pure-u-1 pure-u-lg-4-5">
			<div class="inner">
				<?php
					if ($displaySuccess) {
						include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
					}
				?>
				<h2>Demonyms</h2>
				<form class="pure-form pure-form-aligned" action="./" method="POST">
					<fieldset>
						<div class="control-group">
							<button id="plus-dn" type="button" class="pure-button pure-button-secondary">Add Demonym</button>
							<button id="minus-dn" type="button" class="pure-button pure-button-secondary">Remove Demonym</button>
						</div>
						<div class="label-group">
							<label>Subscribers</label>
							<label>Online users</label>
						</div>
						<div id="control-row" class="pure-controls">
							<button type="submit" class="pure-button pure-button-primary">Submit</button>
						</div>
					</fieldset>
					<script type="text/javascript">
						var demonyms;
						$.ajaxSetup({async:false});
						$.get("/demonyms/demonyms.php?verbose", function(data) {
							demonyms = JSON.parse(data);
						});
						$.ajaxSetup({async:true});
						target = document.getElementById("control-row");
						var i = 0;
						for (var demonym in demonyms) {
							var dn = demonyms[demonym];
							var nodes = addDemonym();
							nodes.subs.value = dn.subscribers;
							nodes.users.value = dn.online;
							nodes.subs.defaultValue = dn.subscribers;
							nodes.users.defaultValue = dn.online;
						}

						// Bind the buttons
						$("#plus-dn").on("click", function(ev) {
							addDemonym();
						});
						$("#minus-dn").on("click", function(ev) {
							subtractDemonym();
						});

						function addDemonym() {
							var div = document.createElement("DIV");
							div.className = "pure-control-group";
							var subs = document.createElement("INPUT");
							subs.className = "demonym";
							subs.type = "text";
							subs.name = "demonym" + i + "[]";
							div.appendChild(subs);
							var users = document.createElement("INPUT");
							users.className = "demonym";
							users.type = "text";
							users.name = "demonym" + i + "[]";
							div.appendChild(users);
							target.parentElement.insertBefore(div, target);
							i++;
							return {"subs": subs, "users": users};
						}

						function subtractDemonym() {
							target.parentElement.removeChild(target.parentElement.childNodes[i + 4]);
							i--;
						}
					</script>
				</form>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>