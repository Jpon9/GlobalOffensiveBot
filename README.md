GlobalOffensiveBot
==================

Created by /u/Jpon9 and /u/Tremaux

GlobalOffensiveBot is a program for maintaining and adding features to the /r/GlobalOffensive sidebar.  The webpanel is a more broad scoped project as it is used for hosting any data useful to the rest of the mod team, though its primary purpose is to allow all members of the mod team to easily configure the bot.  This project could easily be modified to benefit alternative subreddits (a modified version is already in use on /r/Halo and /r/Poker), but this project is geared specifically towards benefitting /r/GlobalOffensive.

Futher explanation of how the bot operates will be added here in the future.

###Dependencies

    sudo pip install cssmin									# Minifies the CSS we upload
    sudo pip install praw									# Reddit API wrapper
    sudo pip install --upgrade google-api-python-client		# Google API wrapper
    sudo pip install python-dateutil						# Python datetime utility