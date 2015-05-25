import json
import os, sys
import praw

user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"
settings = None

def refreshSettings():
	global settings
	global r
	settings = json.loads(open(base_path + "config/settings.json", 'r').read())['settings']
	settings['target_subreddit'] = r.get_subreddit(settings['target_subreddit'])

def getSettings():
	global settings
	return settings
	
refreshSettings()