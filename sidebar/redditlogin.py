import praw #thirdparty
from settings import getSettings
from accounts import getAccounts

botAccounts = getAccounts()
user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)
r.login(botAccounts['__MODERATORACCOUNT__'], botAccounts[botAccounts['__MODERATORACCOUNT__']])
for subreddit in r.get_my_moderation():
	if subreddit.display_name.lower() == getSettings()['target_subreddit']:
		target_subreddit = subreddit
		break