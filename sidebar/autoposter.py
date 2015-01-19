###Reddit weekly post scheduler, ezpz version
import praw
import time
import datetime
import dateutil
from dateutil.relativedelta import *
from dateutil.tz import *
import json
import os
import io
from settings import getSettings
from accounts import getAccounts

user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)

subreddit=getSettings()["target_subreddit"]
#subreddit='GlobalOffensive'
###Scheduled weekly ,currently using BST timezone, scheduled to post 4h before event
#EUtime=[1,15,30] #weekday(0 -monday,6 -sunday) ||| hour ||| minute #15.30
#NAtime=[1,22,30] #weekday(0 -monday,6 -sunday) ||| hour ||| minute
#playtestdetailink="http://www.reddit.com/user/csgocomnights"
import reddit
canonical_stickytitle = "DEFAULT STICKYTITLE"

base_path = os.getcwd() + "/"
#sticky = json.loads(open(base_path + "cache/stickies.json", 'r').read())

def autoposterfunc():
	global canonical_stickytitle
	botAccounts = getAccounts()
	#print "AutoPoster running..."
	while True:
		poster_accounts = reddit.poster_accounts
		sticky = reddit.sticky
		for post in sticky["stickies"]:
			links=[]
			#if ["link","something","nopost"] not in post["type"]:
			if post["master_disable"] == False:
				#if post["type"] != "link":
				if "link" not in post["type"]:
					if post["postedflag"] == 0:
						if (datetime.datetime.now(tzutc()).today().weekday() == post["time"][0]) and \
							(datetime.datetime.now(tzutc()).hour == post["time"][1]) and \
							(datetime.datetime.now(tzutc()).minute == post["time"][2]):
							if post.has_key("poster_account"):
								if poster_accounts.has_key(post["poster_account"]):
									account=str(post["poster_account"])
								else:
									account='GlobalOffensiveBot'
							elif not post.has_key("poster_account"):
								account='GlobalOffensiveBot'
							
							if str(r.user) != account:		## check if current logged in account is different from wanted one
								r.clear_authentication()  	## logs out of reddit accounts
								if account != 'GlobalOffensiveBot':
									r.login(str(post["poster_account"]), str(poster_accounts[post["poster_account"]])) # username and password of poster
								else:
									r.login(botAccounts['__MODERATORACCOUNT__'], botAccounts[botAccounts['__MODERATORACCOUNT__']])
					
							r.submit(subreddit, post["post_title"], text=post["body"]) #.sticky()	
							print "post sent:",post["post_title"]
							post["postedflag"] = 1
							time.sleep(2)
							gen = r.get_redditor(account).get_submitted(limit=2)
							for link in gen:
								#print link.url
								links.append(link.url)
							post["postlink"]=str(links[0])
							with io.open(base_path + "cache/stickies.json", 'w', encoding='utf-8') as f:
								f.write(unicode(json.dumps(sticky, ensure_ascii=False, indent=4, separators=(',', ': '))))
					if post["postedflag"] == 1 and (datetime.datetime.now().minute > post["time"][2]):
						relativetime=dateutil.relativedelta.relativedelta(weekday=post["time"][0],hour=post["time"][1],minute=post["time"][2],second=0,microsecond=0)
						if (datetime.datetime.now(tzutc()) > (datetime.datetime.now(tzutc()) + relativetime + datetime.timedelta(hours=post["duration_hours"]))):
							post["postedflag"] = 0
							if "non_recurring" in post["type"]:
								post["master_disable"] = True
							with io.open(base_path + "cache/stickies.json", 'w', encoding='utf-8') as f:
								f.write(unicode(json.dumps(sticky, ensure_ascii=False, indent=4, separators=(',', ': '))))
								
		time.sleep(1)		