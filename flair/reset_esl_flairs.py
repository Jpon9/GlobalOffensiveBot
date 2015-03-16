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

num_removed = 0

to_remove = ['fmesports','reason','ibp','denial','liquid','gorgntv','fragbite','gosugamers','vakarm','99damage']

for flair in flair_generator:
	if flair['flair_css_class'] == None:
		continue

	for tr in to_remove:
		if tr in flair['flair_css_class']:
			flairs.append({'user': flair['user'], 'flair_css_class': '', 'flair_text': ''})
			num_removed += 1

r.set_flair_csv('GlobalOffensive', flairs)

print str(num_removed) + " flairs have been cleared."