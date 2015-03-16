# Python std imports
import io
import json
import os
import time
# Third part imports
import praw
# Project imports
from settings import getSettings

base_path = os.getcwd() + "/"

# Updates the "Users online..." and "Subscribers..." fields on the sidebar via the CSS
def GetHeader():
	metadata = json.loads(open(base_path + "cache/metadata.json", 'r').read())
	numOfHeaders = getSettings()['num_of_headers']

	if metadata["header_cycle"]["current_index"] > numOfHeaders:
		metadata["header_cycle"]["current_index"] = 0
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	if int(time.time()) - int(metadata["header_cycle"]["last_updated"]) > 30 * 30:
		# Grabs the new index for the active demonym
		metadata["header_cycle"]["current_index"] = (metadata["header_cycle"]["current_index"] + 1) % numOfHeaders
		metadata["header_cycle"]["last_updated"] = int(time.time())
		# Write the metadata object
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	# Returns the Reddit CSS %%variable%%
	return "%%header" + str(metadata["header_cycle"]["current_index"] + 1) + "%%"