function timeDiff(first, second) {
    var diff = Math.round(Math.abs(first - second)) / 1000;
    if (diff % 1 <= 0.00001 &&
             diff % 1 >= -0.00001) {
        diff = diff + ".000";
    } else if (diff / 0.1 % 1 <= 0.00001 ||
            (diff / 0.1 % 1 >= .99999 &&
            diff / 0.1 % 1 < 1)) {
        diff = diff + "00";
    } else if (diff / 0.01 % 1 <= 0.00001 ||
        (diff / 0.01 % 1 >= .99999 &&
        diff / 0.01 % 1 < 1)) {
        diff = diff + "0";
    }
    return diff;
}

function loading() {
    var start = new Date().getTime();
    var oldElapsed = 0;
    var elapsed = 0;
    console.log("Beginning to load...");
    var intervalId = setInterval(function() {
            oldElapsed = elapsed;
            elapsed = timeDiff(start, new Date().getTime());
            if (document.getElementById("loadingTimer") != null) {
                document.getElementById("loadingTimer").innerHTML = elapsed + " seconds";
            }
            if (elapsed == 0 && oldElapsed == 0) {
                newMessage("And that&#39;s not a long time!", "loadingDesc");
            } else if (elapsed >= 3 && oldElapsed <= 3) {
                newMessage("And that&#39;s pretty much average", "loadingDesc");
            } else if (elapsed >= 7.5 && oldElapsed <= 7.5) {
                newMessage("Which is fine, we're just running a bit slow today", "loadingDesc");
            } else if (elapsed >= 15 && oldElapsed <= 15) {
                newMessage("Okay, this is taking a bit longer than usual, I\&#39;m sorry", "loadingDesc");
            } else if (elapsed >= 22 && oldElapsed <= 22) {
                newMessage("Jeez, what is taking so long?", "loadingDesc");
            } else if (elapsed >= 28 && oldElapsed <= 28) {
                newMessage("Something probably went wrong... let me look into it...", "loadingDesc");
            } else if (elapsed >= 35) {
                $("#stats-holder").fadeOut(550, function() {
                    var statsholder = document.getElementById("stats-holder");
                    while (statsholder.hasChildNodes()) {
                        statsholder.removeChild(statsholder.lastChild);
                    }
                    statsholder.innerHTML = '<p class="loading">Could Not Load</p><p class="loading"><span id="loadingDesc"></span></p>';
                    document.getElementById("loadingDesc").innerHTML = "Okay, we\'re calling it quits.  Either something is down or we\'re experiencing technical difficulties.  Try again later?";
                    clearInterval(intervalId);
                    $("#stats-holder").fadeIn(550);
                });
            }
        },
        17
    );
    return intervalId;
}

function newMessage(msg, id) {
    $("#" + id).fadeOut(350, function() {
        document.getElementById(id).innerHTML = msg;
        $("#" + id).fadeIn(250);
    });
}