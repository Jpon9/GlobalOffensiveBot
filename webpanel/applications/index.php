<?php
	$title = "Mod Applications";
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
			<h2>Moderator Applications from Google</h2>
			<table id="applications">
				<tr class="title-row">
					<td>#</td>
					<td>Reddit Profile</td>
					<td>Steam Profile</td>
					<td>Age</td>
					<td>Timezone</td>
					<td>Introduction</td>
					<td>Attributes</td>
					<!--<td>Coding Experience</td>-->
				</tr>
			</table>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
		<script type="text/javascript">
			var applications = [];
			$.ajaxSetup({async:false});
			$.get("./applications.tsv", function(data) {
				data = data.split("\n");
				var i = 0;
				for (var app in data) {
					if (i < 4) {
						i += 1;
						continue;
					}
					app = data[app].split("\t");
					applications.push({
						"timestamp": app[0],
						"reddit_profile": app[1],
						"age": app[2],
						"attributes": app[3].split(", "),
						"coding_experience": app[4].split(", "),
						"introduction": app[5],
						"steam_profile": app[6],
						"timezone": app[7]
					});
				}
			});
			$.ajaxSetup({async:true});
			var introsLength = 0;
			var target = document.getElementById("applications");
			var i = 0;
			for (var app in applications) {
				var str = applications[app].reddit_profile;
				if (str.substr(str.length - 1, str.length) === '/') {
					str = str.substr(0, str.length - 1);
				}
				i += 1;
				var tr = document.createElement("TR");
				var index = document.createElement("TD");
				var indexLink = document.createElement("A");
				indexLink.name = i;
				indexLink.href = "#" + i;
				indexLink.innerHTML = i;
				indexLink.appendChild(index);
				var redditAccount = document.createElement("TD");
				redditAccountLink = document.createElement("A");
				redditAccountLink.href = applications[app].reddit_profile;
				redditAccountLink.innerHTML = str.split("/")[str.split("/").length - 1];
				redditAccount.appendChild(redditAccountLink);
				var steamProfile = document.createElement("TD");
				steamProfileLink = document.createElement("A");
				steamProfileLink.href = applications[app].steam_profile;
				steamProfileLink.innerHTML = "Steam Profile";
				steamProfile.appendChild(steamProfileLink);
				var age = document.createElement("TD");
				age.innerHTML = applications[app].age;
				var timezone = document.createElement("TD");
				timezone.innerHTML = applications[app].timezone;
				var introduction = document.createElement("TD");
				introduction.innerHTML = applications[app].introduction.replace(/\s{2,}/g, "<br><br>");
				introsLength += applications[app].introduction.replace(/\s{2,}/g, "<br><br>").split(' ').length + 1;
				var attributes = document.createElement("TD");
				attributes.innerHTML = applications[app].attributes.join("<br>");
				var coding_experience = document.createElement("TD");
				coding_experience.innerHTML = applications[app].coding_experience.join("<br>");
				tr.appendChild(indexLink);
				tr.appendChild(redditAccount);
				tr.appendChild(steamProfile);
				tr.appendChild(age);
				tr.appendChild(timezone);
				tr.appendChild(introduction);
				tr.appendChild(attributes);
				//tr.appendChild(coding_experience);
				target.appendChild(tr);
			}
			console.log("Introductions word count: " + introsLength);
		</script>
	</body>
</html>