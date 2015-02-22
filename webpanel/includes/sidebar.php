<div id="sidebar">
	<h2>Status</h2>
	<ul>
		<li>The bot is <span id="bot-status" class="status-indeterminate">Schr&ouml;dinger's bot</span></li>
		<li>Updates in <span id="bot-updates-in">...</span></li>
	</ul>
	<h2>Configurations</h2>
	<ul>
		<li><a href="/basic" <?php if ($title == "Basic Settings") { echo 'class="active"'; } ?>>Basic Config</a></li>
		<li><a href="/sidebar" <?php if ($title == "Sidebar") { echo 'class="active"'; } ?>>Sidebar</a></li>
		<li><a href="/stylesheet" <?php if ($title == "Stylesheet") { echo 'class="active"'; } ?>>Stylesheet</a></li>
		<li><a href="/demonyms" <?php if ($title == "Demonyms") { echo 'class="active"'; } ?>>Demonyms</a></li>
		<li><a href="/notices" <?php if ($title == "Notices" || $title == "Notices Help") { echo 'class="active"'; } ?>>Notices</a></li>
	</ul>
	<h2>See Also</h2>
	<ul>
		<li><a href="/applications" <?php if ($title == "Mod Applications") { echo 'class="active"'; } ?>>Moderator Applications</a></li>
		<li><a href="/giveaway" <?php if ($title == "Giveaway") { echo 'class="active"'; } ?>>Giveaway Winners</a></li>
		<li><a href="/giveaway/items" <?php if ($title == "Giveaway Items") { echo 'class="active"'; } ?>>Giveaway Items</a></li>
		<li><a href="/top-commenters" <?php if ($title == "Top Commenters") { echo 'class="active"'; } ?>>Top Commenters (January)</a></li>
		<li><a href="/changelog" <?php if ($title == "Changelog") { echo 'class="active"'; } ?>>Changelog</a></li>
		<li><a href="/error-log" <?php if ($title == "Error Log") { echo 'class="active"'; } ?>>Error Log</a></li>
		<li><a href="/webpanel-log" <?php if ($title == "Webpanel Log") { echo 'class="active"'; } ?>>Webpanel Log</a></li>
		<li><a href="/help" <?php if ($title == "Help") { echo 'class="active"'; } ?>>Help</a></li>
	</ul>
	<a href="#" id="scroll-top" style="opacity:0;display:none">Scroll to top</a>
</div>
<script type="text/javascript">
	var isScrolling = false;
	$(document).on("scroll", function() {
		if (isScrolling) { return; }
		var target = document.getElementById("scroll-top");
		var windowHeight = $(window).height();
		var doc = document.documentElement;
		var scrollHeight = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
		if (windowHeight / 2 - scrollHeight < 0) {
			if (target.style.opacity == 0) {
				target.style.display = "inherit";
				$(target).animate({opacity:1}, 200);
			}
		} else {
			if (target.style.opacity == 1) {
				$(target).animate({opacity:0}, 200, function() {
					target.style.display = "none";
				});
			}
		}
	});

	$("#scroll-top").on("click", function() {
		isScrolling = true;
		var target = document.getElementById("scroll-top");
		$(target).animate({opacity:0}, 200, function() {
			target.style.display = "none";
		});
		$("html, body").animate({scrollTop: 0}, 1000, function() {
			isScrolling = false;
		});
	});
</script>