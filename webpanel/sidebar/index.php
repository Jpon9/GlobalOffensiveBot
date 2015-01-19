<?php
	include_once("./sidebar.php");

	$title = "Sidebar";
	$displaySuccess = true;

	if (isset($_POST['template'])) {
		$sidebar = getSidebar();

		foreach ($sidebar['chunks'] as $chunkName => $chunkBody) {
			if (!isset($_POST[$chunkName])) {
				$allChunksSet = false;
				break;
			}
		}

		updateSidebar($sidebar);

		unset($_POST);
	} else {
		$displaySuccess = false;
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
				$sidebar = getSidebar();

				if ($displaySuccess) {
					include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
				}
			?>
			<h2>Sidebar</h2>
			<form action="./" method="POST">
				<table>
					<tr>
						<td><label>Sidebar Section Order</label></td>
						<td><textarea name="template"><?php echo $sidebar['template']; ?></textarea></td>
					</tr>
					<!-- Chunks will be inserted here -->
					<tr id="final-row">
						<td><input type="submit" value="Submit"><input type="reset"></td>
					</tr>
					<script type="text/javascript">
						var sidebar;
						$.ajaxSetup({async:false});
						$.get("/sidebar/sidebar.php?verbose", function(data) {
							sidebar = JSON.parse(data);
						});
						$.ajaxSetup({async:true});
						target = document.getElementById("final-row");
						for (var chunk in sidebar['chunks']) {
							var ch = sidebar['chunks'][chunk];
							var tr = document.createElement("TR");
							var td = document.createElement("TD");
							var label = document.createElement("LABEL");
							label.innerHTML = ch.name;
							td.appendChild(label);
							tr.appendChild(td);
							var td2 = document.createElement("TD");
							tr.appendChild(td2);
							var ta = document.createElement("TEXTAREA");
							ta.className = "sidebar-chunk";
							ta.name = ch.name;
							ta.innerHTML = ch.body;
							td2.appendChild(ta);
							target.parentElement.insertBefore(tr, target);
						}
					</script>
				</table>
			</form>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>