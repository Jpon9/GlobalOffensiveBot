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
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/panel.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="body">
			<?php
				if ($displaySuccess) {
					include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
				}
			?>
			<h2>Demonyms</h2>
			<form action="./" method="POST">
				<table>
					<tr>
						<td class="button-row"><input id="plus-dn" type="button" value="Add Demonym"><input id="minus-dn" type="button" value="Subtract Demonym"></td>
					</tr>
					<tr>
						<td>Subscribers</td>
						<td>Online users</td>
					</tr>
					<!-- Demonyms will be inserted here -->
					<tr id="final-row">
						<td><input type="submit" value="Submit"><input type="reset"></td>
					</tr>
					<script type="text/javascript">
						var demonyms;
						$.ajaxSetup({async:false});
						$.get("/demonyms/demonyms.php?verbose", function(data) {
							demonyms = JSON.parse(data);
						});
						$.ajaxSetup({async:true});
						target = document.getElementById("final-row");
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
							var tr = document.createElement("TR");
							var td = document.createElement("TD");
							var subs = document.createElement("INPUT");
							subs.className = "demonym";
							subs.type = "text";
							subs.name = "demonym" + i + "[]";
							td.appendChild(subs);
							tr.appendChild(td);
							var td2 = document.createElement("TD");
							var users = document.createElement("INPUT");
							users.className = "demonym";
							users.type = "text";
							users.name = "demonym" + i + "[]";
							td2.appendChild(users);
							tr.appendChild(td2);
							target.parentElement.insertBefore(tr, target);
							i++;
							return {"subs": subs, "users": users};
						}

						function subtractDemonym() {
							target.parentElement.removeChild(target.parentElement.childNodes[i + 5]);
							i--;
						}
					</script>
				</table>
			</form>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>