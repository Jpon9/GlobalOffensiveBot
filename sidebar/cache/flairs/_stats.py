import collections
import io
import json
import os, sys
from os import listdir
from os.path import isfile, join

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

flairs = {}
flair_sources = [ f for f in listdir(base_path) if isfile(join(base_path,f)) and f[0:7] == 'flairs_' ]

for flair_source in flair_sources:
	flairs[flair_source.replace('.json','')] = json.loads(open(join(base_path, flair_source), 'r').read())

final_count = {}

for date, flair_dump in flairs.iteritems():
	final_count[date] = {}
	for flair in flair_dump:
		if flair['flair_css_class'] == None:
			continue
		if flair['flair_css_class'][0:3] != 'fan':
			continue

		if flair['flair_css_class'] in final_count[date]:
			final_count[date][flair['flair_css_class']] += 1
		else:
			final_count[date][flair['flair_css_class']] = 1

# Write the list of flairs to json for future use
with io.open(base_path + "_dump-fanonly.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(collections.OrderedDict(sorted(final_count.items())), ensure_ascii=False, indent=4, separators=(',', ': '))))