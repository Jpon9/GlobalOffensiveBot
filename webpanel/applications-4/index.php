<?php
	$title = "Mod Applications";
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/reset.css">
		<link rel="stylesheet" type="text/css" href="/style/applications.css">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/moment.js"></script>
	</head>
	<body class="pure-g">
		<h2>Moderator Applications (Round #4)</h2>
		<p>There are currently <span id="num-of-apps"></span> applications available.</p>
		<p>The list of applications was last updated on <span id="last-updated"></span>.</p>
		<p>The Phase 2 page can be found <a href="./phase-2">here</a>.</p>
		<table class="stats">
			<tr>
				<td>Your Votes</td>
				<td id="apps-voted-on">loading...</td>
			</tr>
			<tr>
				<td>Your Approvals</td>
				<td id="your-approvals">loading...</td>
			</tr>
			<tr>
				<td>Your Neutrals</td>
				<td id="your-neutrals">loading...</td>
			</tr>
			<tr>
				<td>Your Denials</td>
				<td id="your-denials">loading...</td>
			</tr>
		</table>
		<table class="stats">
			<tr>
				<td>Total Votes</td>
				<td id="total-votes">loading...</td>
			</tr>
			<tr>
				<td>Approvals</td>
				<td id="approvals">loading...</td>
			</tr>
			<tr>
				<td>Neutrals</td>
				<td id="neutrals">loading...</td>
			</tr>
			<tr>
				<td>Denials</td>
				<td id="denials">loading...</td>
			</tr>
		</table>
		<div id="applications">

		</div>
		<script type="text/javascript">
			var applications = [];
			var votes = [];
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
						"timezone": app[2],
						"age": app[3],
						"code_xp": app[4],
						"admin_xp": app[5],
						"thoughts_on_comnight": app[6],
						"thoughts_on_newbiet": app[7],
						"biography": app[8],
						"time_on_subreddit": app[9],
						"thoughts_on_community": app[10]
					});
				}
			});
			$.get("./get-votes.php", function(data) {
				votes = JSON.parse(data);
			});
			$.get("./get-tallies.php", function(data) {
				tallies = JSON.parse(data);
			});
			$.ajaxSetup({async:true});
			var introsLength = 0;
			var target = document.getElementById("applications");
			var i = 0;

			var approvals = 0;
			var neutrals = 0;
			var denials = 0;
			// Get meta stats
			for (var j in tallies) {
				approvals += tallies[j]['approve'];
				neutrals += tallies[j]['neutral'];
				denials += tallies[j]['deny'];
			}

			var yourApprovals = 0;
			var yourNeutrals = 0;
			var yourDenials = 0;
			// Get meta stats
			for (var j in tallies) {
				yourApprovals += votes[j] === "approve" ? 1 : 0;
				yourNeutrals += votes[j] === "neutral" ? 1 : 0;
				yourDenials += votes[j] === "deny" ? 1 : 0;
			}

			document.getElementById("num-of-apps").innerHTML = applications.length;
			document.getElementById("apps-voted-on").innerHTML = Object.keys(votes).length;
			document.getElementById("your-approvals").innerHTML = yourApprovals;
			document.getElementById("your-neutrals").innerHTML = yourNeutrals;
			document.getElementById("your-denials").innerHTML = yourDenials;
			var lastUpdated = 1433362221 * 1000;
			document.getElementById("last-updated").innerHTML = moment(lastUpdated).format("MMMM Do") + " at " + moment(lastUpdated).format("h:mma");
			document.getElementById("total-votes").innerHTML = approvals + neutrals + denials;
			document.getElementById("approvals").innerHTML = approvals;
			document.getElementById("neutrals").innerHTML = neutrals;
			document.getElementById("denials").innerHTML = denials;

			// Define helper functions
			function createElem(e, p) {
				var elem = document.createElement(e);
				p.appendChild(elem);
				return elem;
			}

			function createTextRow(parent, label, data) {
				var row = createElem("TR", parent);
				  createElem("TD", row).innerHTML = label;
				  var d = createElem("TD", row);
				  d.innerHTML = data;
			}

			var defLoadNum = 15;
			var wordCount = 0;

			for (var k in applications) {
				wordCount += applications[k]["code_xp"].split(' ').length + 1;
				wordCount += applications[k]["admin_xp"].split(' ').length + 1;
				wordCount += applications[k]["thoughts_on_comnight"].split(' ').length + 1;
				wordCount += applications[k]["thoughts_on_newbiet"].split(' ').length + 1;
				wordCount += applications[k]["biography"].split(' ').length + 1;
				wordCount += applications[k]["thoughts_on_community"].split(' ').length + 1;
			}

			console.log("Word Count: " + wordCount);

			function loadApplications(num, to) {
				to = typeof to !== 'undefined' && to !== 0 ? to : defLoadNum;
				var numToLoad = applications.length < to + i ? applications.length : to + i;
				console.log(numToLoad);
				console.log(applications[num]);

				for (var j = num; j < numToLoad; ++j) {
					var str = applications[j].reddit_profile;
					if (str.substr(str.length - 1, str.length) === '/') {
						str = str.substr(0, str.length - 1);
					}
					var cleanUsername = str.split("/")[str.split("/").length - 1];
					i += 1;
					var container = createElem("DIV", target);
					var applicant = createElem("TABLE", container);
					//createTextRow(applicant, "Timestamp", applications[j].timestamp);
					var profileLinkRow = createElem("TR", applicant);
					  createElem("TD", profileLinkRow).innerHTML = "Reddit Profile";
					  var profileLinkData = createElem("TD", profileLinkRow);
					    var profileLinkA = createElem("A", profileLinkData);
					    profileLinkA.href = applications[j].reddit_profile;
					    profileLinkA.innerHTML = cleanUsername;
					    profileLinkA.className = "username";
					    var permalink = createElem("A", profileLinkData);
					    permalink.innerHTML = "#" + (j + 1);
					    permalink.className = "permalink";
					    permalink.name = (j + 1);
					    permalink.href = "#" + (j + 1);
					createTextRow(applicant, "Timezone", applications[j].timezone);
					createTextRow(applicant, "Age", applications[j].age);
					createTextRow(applicant, "Coding Experience", applications[j].code_xp.replace(/\s{2,}/g, "<br><br>"));
					createTextRow(applicant, "Server Experience", applications[j].admin_xp.replace(/\s{2,}/g, "<br><br>"));
					createTextRow(applicant, "Thoughts on Community Night", applications[j].thoughts_on_comnight.replace(/\s{2,}/g, "<br><br>"));
					createTextRow(applicant, "Thoughts on Newbie Thursday", applications[j].thoughts_on_newbiet.replace(/\s{2,}/g, "<br><br>"));
					createTextRow(applicant, "Autobiography", applications[j].biography.replace(/\s{2,}/g, "<br><br>"));
					createTextRow(applicant, "Time on /r/GlobalOffensive", applications[j].time_on_subreddit);
					createTextRow(applicant, "Thoughts on Community", applications[j].thoughts_on_community.replace(/\s{2,}/g, "<br><br>"));
					
					var verdict = "";
					for (var u in votes) {
						if (u === cleanUsername) {
							verdict = votes[u];
						}
					}
					var talliedVotes = [];
					for (var u in tallies) {
						if (u === cleanUsername) {
							talliedVotes = tallies[u];
						}
					}

					var voteContainer = createElem("DIV", container);
					voteContainer.className = "vote-container" + (verdict !== "" ? " chosen" : "");
					// The vote buttons
					var approve = createElem("DIV", voteContainer);
					  var approveImg = createElem("IMG", approve);
					  approveImg.src = "/images/approve.png";
					  approveImg.alt = "Approve";
					  approveImg.className = verdict === "approve" ? "chosen" : "";
					  var approveVoteWeight = createElem("SPAN", approve);
					  approveVoteWeight.className = "approve-vote-weight";
					  approveVoteWeight.innerHTML = talliedVotes['approve'] != null ? talliedVotes['approve'] : 0;
					var neutral = createElem("DIV", voteContainer);
					  var neutralImg = createElem("IMG", neutral);
					  neutralImg.src = "/images/neutral.png";
					  neutralImg.alt = "Neutral";
					  neutralImg.className = verdict === "neutral" ? "chosen" : "";
					  var neutralVoteWeight = createElem("SPAN", neutral);
					  neutralVoteWeight.className = "neutral-vote-weight";
					  neutralVoteWeight.innerHTML = talliedVotes['neutral'] != null ? talliedVotes['neutral'] : 0;
					var deny = createElem("DIV", voteContainer);
					  var denyImg = createElem("IMG", deny);
					  denyImg.src = "/images/deny.png";
					  denyImg.alt = "Deny";
					  denyImg.className = verdict === "deny" ? "chosen" : "";
					  var denyVoteWeight = createElem("SPAN", deny);
					  denyVoteWeight.className = "deny-vote-weight";
					  denyVoteWeight.innerHTML = talliedVotes['deny'] != null ? talliedVotes['deny'] : 0;
					
					function voteClick(ev) {
						// Gather data for the vote
						var application = ev.currentTarget.parentNode.parentNode.parentNode.childNodes[0];
						var username = $(application).find(".username")[0].innerHTML;
						var verdict = ev.currentTarget.alt.toLowerCase();

						// Cast the vote
						$.get("./cast-vote.php?user=" + username + "&verdict=" + verdict);

						// Visual effect
						var voteContainer = ev.currentTarget.parentNode.parentNode;
						var chosenOnes = $(voteContainer).find(".chosen");//.removeClass("chosen");
						if (chosenOnes[0] !== undefined) {
							$(chosenOnes[0].parentNode).find("span")[0].innerHTML -= 1;
							chosenOnes.removeClass("chosen");
						}
						ev.currentTarget.className = "chosen";
						var newVoteWeight = $(ev.currentTarget.parentNode).find("span")[0];
						newVoteWeight.innerHTML = parseInt(newVoteWeight.innerHTML) + 1;
						$(voteContainer).addClass('chosen');
					}

					$(approveImg).click(voteClick);
					$(neutralImg).click(voteClick);
					$(denyImg).click(voteClick);
				}
			}

			$(document).scroll(function() {
				var documentY = $(document).height();
				var doc = document.documentElement;
				var scrollY = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
				
				if (scrollY > documentY * 0.85) {
					loadApplications(i);
				}
			});

			var currentUrl = window.location.href.toString();
			var plmi = currentUrl.search("#"); // permalink marker index
			if (plmi !== -1) {
				currentUrl = currentUrl.substr(plmi + 1, currentUrl.length);
			}
			var loadTo = plmi !== -1 ? currentUrl - 1 : 0;
			loadTo = loadTo - (loadTo % defLoadNum) + defLoadNum;
			loadApplications(0, loadTo);

			if (plmi !== -1) {
				var child = document.getElementById("applications").children[loadTo];
				var scrollTo = child.children[0].getBoundingClientRect().top;
				$("html, body").animate({scrollTop: scrollTo}, 0);
			}
		</script>
	</body>
</html>