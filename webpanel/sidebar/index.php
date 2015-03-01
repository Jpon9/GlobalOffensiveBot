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
		<link rel="stylesheet" type="text/css" href="/style/sidebar.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body class="pure-g">
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="main" class="pure-u-1 pure-u-lg-4-5">
			<div class="inner">
				<?php
					$sidebar = getSidebar();

					if ($displaySuccess) {
						include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
					}
				?>
				<h2>Sidebar</h2>
				<form class="pure-form pure-form-aligned">
					<fieldset>
						<div class="pure-control-group">
							<label for="template">Sidebar Section Order</label>
							<textarea class="small" name="template"><?php echo $sidebar['template']; ?></textarea>
						</div>
						<div id="controls-row" class="pure-controls">
							<button type="submit" class="pure-button pure-button-primary">Submit</button>
						</div>
					</fieldset>
				</form>
				<script type="text/javascript">
					var sidebar;
					$.ajaxSetup({async:false});
					$.get("/sidebar/sidebar.php?verbose", function(data) {
						sidebar = JSON.parse(data);
					});
					$.ajaxSetup({async:true});
					target = document.getElementById("controls-row");
					for (var chunk in sidebar['chunks']) {
						var ch = sidebar['chunks'][chunk];
						var div = document.createElement("DIV");
						div.className = "pure-control-group";
						var label = document.createElement("LABEL");
						label.innerHTML = ch.name;
						var ta = document.createElement("TEXTAREA");
						ta.className = "sidebar-chunk";
						ta.name = ch.name;
						ta.innerHTML = ch.body;
						div.appendChild(label);
						div.appendChild(ta);
						target.parentElement.insertBefore(div, target);
					}
				</script>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>