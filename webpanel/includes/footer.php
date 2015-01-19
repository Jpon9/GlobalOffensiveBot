<script type="text/javascript">
	var botStatus = "";

	// Get bot status
	function updateBotStatus() {
		var status = "indeterminate";
		$.ajaxSetup({async:false});
		$.get("/includes/getBotStatus.php?verbose", function(data) {
			status = JSON.parse(data).status;
		});
		$.ajaxSetup({async:true});
		var target = document.getElementById("bot-status");
		target.innerHTML = status;
		target.className = "status-" + status;
		botStatus = status;
	}
	updateBotStatus();

	// Get bot metadata
	function updateMetadata() {
		$.get("/includes/getBotMetadata.php?verbose", function(data) {
			metadata = JSON.parse(data);

			var target = document.getElementById("bot-updates-in");

			updateBotStatus();

			if (botStatus === "online") {
				timer(target, metadata.last_update_completed);
			} else {
				killTimers();
				target.innerHTML = "...";
			}
		});
	}
	updateMetadata();

	setInterval(function() { updateMetadata(); }, 10000);
</script>