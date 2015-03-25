import io
import json
import os

base_path = os.getcwd() + "/"
flairs = json.loads(open(base_path + "cache/flairs.json", 'r').read())

verified_flairs = {}

for flair in flairs:
	if flair['flair_css_class'] == None:
		continue
	if "rank" in flair['flair_css_class']:
		continue
	if "fan" in flair['flair_css_class']:
		continue

	if flair['flair_css_class'] in verified_flairs:
		verified_flairs[flair['flair_css_class']].append({flair['user']: flair['flair_text']})
	else:
		verified_flairs[flair['flair_css_class']] = [{flair['user']: flair['flair_text']}]

# Write the list of flairs to json for future use
with io.open(base_path + "/cache/verified-flairs.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(verified_flairs, ensure_ascii=False, indent=4, separators=(',', ': '))))