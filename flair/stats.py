import io
import json
import os

base_path = os.getcwd() + "/"
flairs = json.loads(open(base_path + "cache/flairs.json", 'r').read())

flair_counts = {}

for flair in flairs:
	if flair['flair_text'] in flair_counts:
		flair_counts[flair['flair_text']] += 1
	else:
		flair_counts[flair['flair_text']] = 1

# Write the list of flairs to json for future use
with io.open(base_path + "/cache/flair-counts.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(flair_counts, ensure_ascii=False, indent=4, separators=(',', ': '))))