import io
import json
import os
import praw

base_path = os.getcwd() + "/"
settings = json.loads(open(base_path + "config/settings.json", 'r').read())

user_agent = ("Flair Purger 1.0 by /u/Jpon9")
r = praw.Reddit(user_agent=user_agent)
r.login(settings['login']['username'], settings['login']['password'])

flairs = []

num_removed = 0

to_remove = json.loads(open(base_path + "cache/flairs-to-purge.json", 'r').read())

for user in to_remove:
	flairs.append({'user': user, 'flair_css_class': '', 'flair_text': ''})
	num_removed += 1

r.set_flair_csv('GlobalOffensive', flairs)

print str(num_removed) + " of " + str(len(to_remove)) + " flairs have been purged."