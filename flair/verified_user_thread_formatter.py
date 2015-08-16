import io
import json
import os, sys

	
# Enter a string, get a markdown-escaped/sanitized string in return
def SanitizeMarkdown(str):
	sanitized = ""
	markdownChars = ['[',']','(',')','`','>','#','*','^']
	for char in str:
		if char in markdownChars:
			sanitized += "\\" + char
		else:
			sanitized += char
	sanitized = sanitized.replace("    ", "")
	return sanitized

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"
flairs = json.loads(open(base_path + "cache/flairs.json", 'r').read())
flair_mappings = json.loads(open(base_path + "config/flair_mappings.json", 'r').read())

finalized_list = {}
names_to_unflair = []

for flair in flairs:
	if flair['flair_css_class'] == None:
		continue
	if "rank" in flair['flair_css_class']:
		continue
	if "fan" in flair['flair_css_class']:
		continue
	if " Player" in flair['flair_text'] and "official" not in flair['flair_css_class']:
		names_to_unflair.append(flair['user'])
		continue

	matches_group = False
	for title, group in flair_mappings.iteritems():
		for css_class in group:
			to_compare = css_class.split(' ')
			matches_class = True
			for c in to_compare:
				if c not in flair['flair_css_class']:
					matches_class = False
			if matches_class:
				if title not in finalized_list:
					finalized_list[title] = {}
				if css_class not in finalized_list[title]:
					finalized_list[title][css_class] = {}
				finalized_list[title][css_class][flair['user']] = flair['flair_text']
				matches_group = True
				break
		if matches_group:
			break

# Write the list of flairs to json for future use
with io.open(base_path + "/cache/verified-flairs-grouped.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(finalized_list, ensure_ascii=False, indent=4, separators=(',', ': '))))
	
# Write the list of users to purge
with io.open(base_path + "/cache/flairs-to-purge.json", 'w+', encoding='utf-8') as f:
	f.write(unicode(json.dumps(names_to_unflair, ensure_ascii=False, indent=4, separators=(',', ': '))))

def lower_if_possible(x):
    try:
        return x.lower()
    except AttributeError:
        return x
	
final_markdown = ""
for title, group in sorted(finalized_list.items(), key=lambda x: map(lower_if_possible, x)):
	header = "##" + SanitizeMarkdown(title) + "\n\n"
	top_row = "User | Title | Flair\n---|---|---\n"
	body = ""
	for css_class, subgroup in sorted(group.items(), key=lambda x: map(lower_if_possible, x)):
		print subgroup
		for user, flair_text in sorted(subgroup.items(), key=lambda x: map(lower_if_possible, x)):
			body += "/u/" + user + " | " + flair_text + " | " + "[](#" + css_class.replace(' ', '-') + ")\n"
	body += "\n"
	final_markdown += header + top_row + body
			
# Write the list of flairs to json for future use
with io.open(base_path + "/cache/verified-flairs-markdown.txt", 'w+', encoding='utf-8') as f:
	f.write(unicode(final_markdown))