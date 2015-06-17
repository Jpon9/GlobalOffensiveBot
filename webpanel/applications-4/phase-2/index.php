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
		<h2>Moderator Applications (Round #4 Phase 2)</h2>
		<p>There are currently <span id="num-of-apps"></span> applications available.</p>
		<div id="applicants">
			<p>List of approved applicants:</p>
			<p id="approved-applicants"></p>
		</div>
		<div id="applications">

		</div>
		<script type="text/javascript">
			var applications = [];
			var votes = [];
			var tallies = [];
			$.ajaxSetup({async:false});
			$.get("../applications.tsv", function(data) {
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
			$.get("../get-votes.php", function(data) {
				votes = JSON.parse(data);
			});
			$.get("../get-tallies.php", function(data) {
				tallies = JSON.parse(data);
			});
			$.ajaxSetup({async:true});
			var introsLength = 0;
			var target = document.getElementById("applications");
			var i = 0;

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
			var numApproved = 0;

			function loadApplications(num, to) {
				to = typeof to !== 'undefined' && to !== 0 ? to : defLoadNum;
				var numToLoad = applications.length < to + i ? applications.length : to + i;
				var approvedApplicants = [];
				
				for (var j = num; j < numToLoad; ++j) {
					if (typeof applications[j] === "undefined") {
						break;
					}
					var str = applications[j].reddit_profile;
					if (str.substr(str.length - 1, str.length) === '/') {
						str = str.substr(0, str.length - 1);
					}
					var cleanUsername = str.split("/")[str.split("/").length - 1];

					var talliedVotes = [];
					for (var u in tallies) {
						if (u === cleanUsername) {
							talliedVotes = tallies[u];
						}
					}
					var approvalRating = talliedVotes['approve'] / (talliedVotes['deny'] + talliedVotes['neutral'] + talliedVotes['approve']);
					console.log(cleanUsername + ": " + approvalRating);
					if (approvalRating < 0.65 || isNaN(approvalRating)) {
						numToLoad += 1;
						continue;
					} else {
						numApproved++;
					}

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
					createTextRow(applicant, "Thoughts on Community", applications[j].thoughts_on_community.replace(/\s{2,}/g, "<br><br>"));var verdict = "";
					
					for (var u in votes) {
						if (u === cleanUsername) {
							verdict = votes[u];
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
					approvedApplicants.push('<a href="#' + (j + 1) + '">' + cleanUsername + '</a>');
				}
				document.getElementById("num-of-apps").innerHTML = numApproved;
				document.getElementById("approved-applicants").innerHTML = approvedApplicants.join(", ");
			}

			var loadTo = applications.length;
			loadApplications(0, loadTo);

			if (plmi !== -1) {
				var child = document.getElementById("applications").children[loadTo];
				var scrollTo = child.children[0].getBoundingClientRect().top;
				$("html, body").animate({scrollTop: scrollTo}, 0);
			}
		</script>
	</body>
</html>