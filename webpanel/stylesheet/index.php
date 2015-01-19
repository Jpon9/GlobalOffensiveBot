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
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/panel.css">
		<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"; ?>
	</head>
	<body>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/nav.php"; ?>
		<div id="body">
			<?php
				$stylesheet = getStylesheet();

				if ($displaySuccess) {
					include $_SERVER['DOCUMENT_ROOT'] . "/includes/success.html";
				}
			?>
			<h2>Stylesheet</h2>
			<form action="./" method="POST">
				<table>
					<tr>
						<td><label>Base Stylesheet</label></td>
						<td><textarea name="stylesheet"><?php echo $stylesheet; ?></textarea></td>
					</tr>
					<tr id="final-row">
						<td><input type="submit" value="Submit"><input type="reset"></td>
					</tr>
				</table>
			</form>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
	</body>
</html>