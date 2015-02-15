import io
import json
import os
import praw

base_path = os.getcwd() + "/"
settings = json.loads(open(base_path + "config/settings.json", 'r').read())

user_agent = ("Flair Reset 1.0 by /u/Jpon9")
r = praw.Reddit(user_agent=user_agent)
r.login(settings['login']['username'], settings['login']['password'])

flair_generator = r.get_flair_list(subreddit='GlobalOffensive', limit=None)

flairs = []

for flair in flair_generator:
	flairs.append(flair)
	print flair['user']

# Write the list of flairs to json for future use
with io.open(base_path + "/cache/flairs.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(flairs, ensure_ascii=False, indent=4, separators=(',', ': '))))