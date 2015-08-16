<?php
	include_once("./stylesheet.php");

	$title = "Stylesheet";
	$displaySuccess = false;

	if (isset($_POST['stylesheet'])) {
		updateStylesheet($_POST['stylesheet']);
		$displaySuccess = true;
		unset($_POST);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/stylesheet.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body class="pure-g">
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="main" class="pure-u-1 pure-u-lg-4-5">
			<div class="inner">
				<?php
					$stylesheet = getStylesheet();

					if ($displaySuccess) {
						include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
					}
				?>
				<h2>Stylesheet</h2>
				<form class="pure-form" action="./" method="POST">
					<fieldset>
						<div class="pure-control-group">
							<textarea name="stylesheet"><?php echo $stylesheet; ?></textarea>
						</div>
						<div class="pure-controls">
							<button type="submit" class="pure-button pure-button-primary">Submit</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>