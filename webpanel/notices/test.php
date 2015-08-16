<?php
	include_once("./notices.php");

	$title = "Notices";
	$displaySuccess = false;

	// If at least one notice exists in POST, rewrite the stickies.json file
	if (isset($_POST["0_notice_title"])) {
		$i = 0;
		$notices = ["stickies" => []];
		$numOfNotices = intval(substr(array_keys($_POST)[count($_POST) - 1], 0, 1)) + 1;
		for ($i = 0; $i < $numOfNotices; ++$i) {
			$base = $i . "_";
			$postDay = $_POST[$base . "post_day"];
			$postHour = $_POST[$base . "post_hour"] + $_POST["timezone"];
			// Convert times to UTC based on the timezone we got from Javascript
			if ($postHour < 0) {
				$postHour += 24;
				$postDay -= 1;
			} else if ($postHour >= 24) {
				$postHour -= 24;
				$postDay += 1;
			}

			// Build the notice
			$notice = [
				"poster_account" => $_POST[$base . "poster_account"],
				"postedflag" => $_POST[$base . "postedflag"],
				"notice_title" => $_POST[$base . "notice_title"],
				"type" => $_POST[$base . "type"],
				"noticetype" => $_POST[$base . "noticetype"],
				"time" => [
					$postDay,
					$postHour,
					$_POST[$base . "post_minute"]
				],
				"duration_hours" => $_POST[$base . "duration_hours"],
				"post_title" => $_POST[$base . "post_title"],
				"postlink" => $_POST[$base . "postlink"],
				"body" => $_POST[$base . "body"],
				"hide_notice" => isset($_POST[$base . "hide_notice"]),
				"master_disable" => isset($_POST[$base . "master_disable"])
			];
			array_push($notices['stickies'], $notice);
		}
		updateNotices($notices);
		$displaySuccess = true;
		unset($_POST);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?> - /r/GlobalOffensive Bot Webpanel</title>
		<link rel="stylesheet" type="text/css" href="/style/notices.css">
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
				<h2>Notices</h2>
				<p>
					Documentation can be found on <a href="/notices/help/">this page.</a>
				</p>
				<form class="pure-form pure-form-aligned" action="./" method="POST">
					<fieldset>
						<input type="hidden" name="timezone" id="timezone" value="">
						<div class="pure-control-group">
							<button id="plus-notice" type="button" class="pure-button pure-button-secondary">Add Notice</button>
							<button id="minus-notice" type="button" class="pure-button pure-button-secondary">Subtract Notice</button>
						</div>
						<div id="notices">
							<h3>Edit Notices</h3>
							<div id="notice-edit" class="pure-g">
								<div class="notice pure-u-1-2">
									<div class="inner-2">
										<h4 onclick="collapse(this)" class="notice-title"><img src="/images/collapse.png" class="arrow collapsed" alt="collapse">Newbie Thursday</h4>
										<div class="collapsible">
											<div class="inner-2">
												<div class="pure-control-group">
													<select name="type">
														<option value="autopost+notice" selected>Autoposted Thread + Notice</option>
														<option value="autopost">Autoposted Thread Only</option>
														<option value="notice">Notice Only</option>
													</select>
												</div>
												<div class="pure-control-group">
													<label>Notice Category</label>
													<select name="category">
														<option value="notice" selected>Notice</option>
														<option value="discussion">Discussion</option>
														<option value="event">Event</option>
													</select>
												</div>
												<div class="pure-control-group">
													<label>Thread Title</label>
													<input type="text" name="title" value="Default Title">
												</div>
												<div class="pure-control-group">
													<label>Poster Account</label>
													<select name="poster_account">
														<option value="GlobalOffensiveBot" selected>GlobalOffensiveBot</option>
														<option value="csgocomnights">csgocomnights</option>
													</select>
												</div>
												<div class="pure-control-group">
													<label>Delete After (hours)</label>
													<input type="text" name="title" value="0">
												</div>
												<div class="pure-control-group">
													<label>Post Time</label>
													<select name="post_day">
														<option value="0">Monday</option>
														<option value="1">Tuesday</option>
														<option value="2">Wednesday</option>
														<option value="3">Thursday</option>
														<option value="4">Friday</option>
														<option value="5">Saturday</option>
														<option value="6">Sunday</option>
													</select>
													<select name="post_hour">
														<option value="0">00</option>
														<option value="1">01</option>
														<option value="2">02</option>
														<option value="3">03</option>
														<option value="4">04</option>
														<option value="5">05</option>
														<option value="6">06</option>
														<option value="7">07</option>
														<option value="8">08</option>
														<option value="9">09</option>
														<option value="10">10</option>
														<option value="11">11</option>
														<option value="12">12</option>
														<option value="13">13</option>
														<option value="14">14</option>
														<option value="15">15</option>
														<option value="16">16</option>
														<option value="17">17</option>
														<option value="18">18</option>
														<option value="19">19</option>
														<option value="20">20</option>
														<option value="21">21</option>
														<option value="22">22</option>
														<option value="23">23</option>
													</select>
													<select name="post_minute">
														<option value="0">00</option>
														<option value="1">01</option>
														<option value="2">02</option>
														<option value="3">03</option>
														<option value="4">04</option>
														<option value="5">05</option>
														<option value="6">06</option>
														<option value="7">07</option>
														<option value="8">08</option>
														<option value="9">09</option>
														<option value="10">10</option>
														<option value="11">11</option>
														<option value="12">12</option>
														<option value="13">13</option>
														<option value="14">14</option>
														<option value="15">15</option>
														<option value="16">16</option>
														<option value="17">17</option>
														<option value="18">18</option>
														<option value="19">19</option>
														<option value="20">20</option>
														<option value="21">21</option>
														<option value="22">22</option>
														<option value="23">23</option>
														<option value="24">24</option>
														<option value="25">25</option>
														<option value="26">26</option>
														<option value="27">27</option>
														<option value="28">28</option>
														<option value="29">29</option>
														<option value="30">30</option>
														<option value="31">31</option>
														<option value="32">32</option>
														<option value="33">33</option>
														<option value="34">34</option>
														<option value="35">35</option>
														<option value="36">36</option>
														<option value="37">37</option>
														<option value="38">38</option>
														<option value="39">39</option>
														<option value="40">40</option>
														<option value="41">41</option>
														<option value="42">42</option>
														<option value="43">43</option>
														<option value="44">44</option>
														<option value="45">45</option>
														<option value="46">46</option>
														<option value="47">47</option>
														<option value="48">48</option>
														<option value="49">49</option>
														<option value="50">50</option>
														<option value="51">51</option>
														<option value="52">52</option>
														<option value="53">53</option>
														<option value="54">54</option>
														<option value="55">55</option>
														<option value="56">56</option>
														<option value="57">57</option>
														<option value="58">58</option>
														<option value="59">59</option>
													</select>
												</div>
												<div class="pure-control-group">
													<label>Notice Start Time</label>
													<select name="post_day">
														<option value="0">Monday</option>
														<option value="1">Tuesday</option>
														<option value="2">Wednesday</option>
														<option value="3">Thursday</option>
														<option value="4">Friday</option>
														<option value="5">Saturday</option>
														<option value="6">Sunday</option>
													</select>
													<select name="post_hour">
														<option value="0">00</option>
														<option value="1">01</option>
														<option value="2">02</option>
														<option value="3">03</option>
														<option value="4">04</option>
														<option value="5">05</option>
														<option value="6">06</option>
														<option value="7">07</option>
														<option value="8">08</option>
														<option value="9">09</option>
														<option value="10">10</option>
														<option value="11">11</option>
														<option value="12">12</option>
														<option value="13">13</option>
														<option value="14">14</option>
														<option value="15">15</option>
														<option value="16">16</option>
														<option value="17">17</option>
														<option value="18">18</option>
														<option value="19">19</option>
														<option value="20">20</option>
														<option value="21">21</option>
														<option value="22">22</option>
														<option value="23">23</option>
													</select>
													<select name="post_minute">
														<option value="0">00</option>
														<option value="1">01</option>
														<option value="2">02</option>
														<option value="3">03</option>
														<option value="4">04</option>
														<option value="5">05</option>
														<option value="6">06</option>
														<option value="7">07</option>
														<option value="8">08</option>
														<option value="9">09</option>
														<option value="10">10</option>
														<option value="11">11</option>
														<option value="12">12</option>
														<option value="13">13</option>
														<option value="14">14</option>
														<option value="15">15</option>
														<option value="16">16</option>
														<option value="17">17</option>
														<option value="18">18</option>
														<option value="19">19</option>
														<option value="20">20</option>
														<option value="21">21</option>
														<option value="22">22</option>
														<option value="23">23</option>
														<option value="24">24</option>
														<option value="25">25</option>
														<option value="26">26</option>
														<option value="27">27</option>
														<option value="28">28</option>
														<option value="29">29</option>
														<option value="30">30</option>
														<option value="31">31</option>
														<option value="32">32</option>
														<option value="33">33</option>
														<option value="34">34</option>
														<option value="35">35</option>
														<option value="36">36</option>
														<option value="37">37</option>
														<option value="38">38</option>
														<option value="39">39</option>
														<option value="40">40</option>
														<option value="41">41</option>
														<option value="42">42</option>
														<option value="43">43</option>
														<option value="44">44</option>
														<option value="45">45</option>
														<option value="46">46</option>
														<option value="47">47</option>
														<option value="48">48</option>
														<option value="49">49</option>
														<option value="50">50</option>
														<option value="51">51</option>
														<option value="52">52</option>
														<option value="53">53</option>
														<option value="54">54</option>
														<option value="55">55</option>
														<option value="56">56</option>
														<option value="57">57</option>
														<option value="58">58</option>
														<option value="59">59</option>
													</select>
												</div>
												<div class="pure-control-group">
													<label>Notice Duration (hours)</label>
													<input type="text" name="title" value="6">
												</div>
												<div class="pure-control-group">
													<label>Frequency</label>
													<select name="frequency">
														<option value="once" selected>Once</option>
														<option value="daily">Daily</option>
														<option value="weekly">Weekly</option>
														<option value="biweekly">Biweekly</option>
														<option value="monthly">Monthly</option>
													</select>
												</div>
												<div class="post-type-selection">
													<p onclick="spExpand(this)" class="active sp-trigger">Self-Post</p><p onclick="lpExpand(this)" class="lp-trigger">Link Post</p>
													<div class="self-post post-type">
														<div class="pure-control-group">
															<label>Sticky for (hours)</label>
															<input type="text" name="sticky_duration" value="6">
														</div>
														<div class="pure-control-group">
															<label>Thread Body</label>
															<textarea name="body"></textarea>
														</div>
													</div>
													<div class="link-post post-type" id="link-post-group">
														<div class="pure-control-group">
															<label>Thread Link</label>
															<input type="text" name="url">
														</div>
													</div>
												</div>
												<div class="pure-control-group">
													<label>Notice Countdown To</label>
													<input type="text" name="countdown_to">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<h3>Preview Notices</h3>
							<div id="notice-preview">
							</div>
						</div>
						<div id="pure-controls">
							<button type="submit" class="pure-button pure-button-primary">Submit</button>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>
		<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
		<script type="text/javascript">
			function collapse(elem) {
				var isCollapsing = false;
				if (elem.getAttribute("data-collapsed") === null) {
					elem.setAttribute("data-collapsed", "false");
				} else if (elem.getAttribute("data-collapsed") == "false") {
					isCollapsing = true;
					elem.setAttribute("data-collapsed", "true");
				} else {
					elem.setAttribute("data-collapsed", "false");
				}

				var index = 0;
				for (e in elem.parentNode.childNodes) {
					if (elem.parentNode.childNodes[e].className == "collapsible") {
						index = e;
					}
				}

				$(elem.parentNode.childNodes[index]).slideToggle();
				elem.childNodes[0].className = "arrow " + (isCollapsing ? "" : "un") + "collapsed";
			}

			function lpExpand(elem) {
				var indexes = getPostGroupIndexes(elem);

				var sp = indexes[0];
				var lp = indexes[1];
				var spTrigger = indexes[2];

				elem.parentNode.childNodes[spTrigger].className = "sp-trigger";
				elem.className = "active lp-trigger";

				animateGroupSwitch(elem.parentNode.childNodes[lp],
					elem.parentNode.childNodes[sp]);
			}

			function spExpand(elem) {
				var indexes = getPostGroupIndexes(elem);

				var sp = indexes[0];
				var lp = indexes[1];
				var lpTrigger = indexes[2];

				elem.parentNode.childNodes[lpTrigger].className = "lp-trigger";
				elem.className = "active sp-trigger";

				animateGroupSwitch(elem.parentNode.childNodes[sp],
					elem.parentNode.childNodes[lp]);
			}

			function getPostGroupIndexes(elem) {
				var sp = 0, lp = 0, otherTrigger = 0;
				
				for (e in elem.parentNode.childNodes) {
					if (elem.parentNode.childNodes[e].className === "self-post post-type") {
						sp = e;
					} else if (elem.parentNode.childNodes[e].className === "link-post post-type") {
						lp = e;
					} else if (("" + elem.parentNode.childNodes[e].className).search("active") !== -1) {
						otherTrigger = e;
					}
				}

				return [sp, lp, otherTrigger];
			}

			function animateGroupSwitch(expanding, collapsing) {
				$(collapsing).slideUp(250, function() {
					$(expanding).slideDown(250);
				});
			}
		</script>
	</body>
</html>