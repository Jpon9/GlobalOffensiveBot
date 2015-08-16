<<<<<<< HEAD
import datetime
import threading
import time
import io
import json
import os, sys
import praw
from redditlogin import r
from settings import getSettings, refreshSettings

next_call = time.time()

def FlairDump():
	# Scheduling
	global next_call
	next_call += 60 * 60
	threading.Timer(next_call - time.time(), FlairDump).start()

	# Get the settings again
	refreshSettings()
	botSettings = getSettings()
	base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

	# Grab the flair generator
	flair_generator = r.get_flair_list(subreddit=botSettings['target_subreddit'], limit=None)
	flairs = []

	for flair in flair_generator:
		if flair['flair_css_class'] == None:
			continue
		#print flair['user']
		flairs.append(flair)

	now = datetime.datetime.now().strftime('%d-%b-%Y_%H.%M') + "_EST"

	filename = "flairs_" + now + ".json"

	print("Flairs cached at " + now)

	# Write the list of flairs to json for future use
	with io.open(base_path + "/cache/flairs/" + filename, 'w+', encoding='utf-8') as f:
=======
import datetime
import threading
import time
import io
import json
import os, sys
import praw
from redditlogin import r
from settings import getSettings, refreshSettings

next_call = time.time()

def FlairDump():
	# Scheduling
	global next_call
	next_call += 60 * 60
	threading.Timer(next_call - time.time(), FlairDump).start()

	# Get the settings again
	refreshSettings()
	botSettings = getSettings()
	base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

	# Grab the flair generator
	flair_generator = r.get_flair_list(subreddit=botSettings['target_subreddit'], limit=None)
	flairs = []

	for flair in flair_generator:
		if flair['flair_css_class'] == None:
			continue
		#print flair['user']
		flairs.append(flair)

	now = datetime.datetime.now().strftime('%d-%b-%Y_%H.%M') + "_EST"

	filename = "flairs_" + now + ".json"

	print("Flairs cached at " + now)

	# Write the list of flairs to json for future use
	with io.open(base_path + "/cache/flairs/" + filename, 'w+', encoding='utf-8') as f:
>>>>>>> 9cac3ffc7ca44891b14144e22e1df4c1b1e14243
		f.write(unicode(json.dumps(flairs, ensure_ascii=False, indent=4, separators=(',', ': '))))