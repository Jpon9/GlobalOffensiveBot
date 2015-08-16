import praw #thirdparty
import sys
from settings import getSettings
from accounts import getAccounts

botAccounts = getAccounts()
botSettings = getSettings()

user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)
r.login(botAccounts['__MODERATORACCOUNT__'], botAccounts[botAccounts['__MODERATORACCOUNT__']])
botIsModerator = False
for subreddit in r.get_my_moderation():
	if subreddit.display_name.lower() == botSettings['target_subreddit'].display_name.lower():
		botIsModerator = True
		break
if botIsModerator == False:
	print "ERROR: Bot account '" + botAccounts['__MODERATORACCOUNT__'] + "' is not a mod on /r/" + botSettings['target_subreddit'].display_name
	print botAccounts['__MODERATORACCOUNT__'] + " is a mod in:\n" + '\n'.join([str(x) for x in r.get_my_moderation()])
	sys.exit(1)
