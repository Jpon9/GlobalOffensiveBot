# Python std imports
import io
import json
import os, sys
import time
# Third part imports
import praw
# Project imports
from settings import getSettings
from metadata import GetMetadata, SetMetadata

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Updates the "Users online..." and "Subscribers..." fields on the sidebar via the CSS
def GetHeader():
	metadata = GetMetadata()
	numOfHeaders = getSettings()['num_of_headers']

	if metadata["header_cycle"]["current_index"] > numOfHeaders:
		metadata["header_cycle"]["current_index"] = 0
		SetMetadata(metadata)
	if int(time.time()) - int(metadata["header_cycle"]["last_updated"]) > 30 * 30:
		# Grabs the new index for the active demonym
		metadata["header_cycle"]["current_index"] = (metadata["header_cycle"]["current_index"] + 1) % numOfHeaders
		metadata["header_cycle"]["last_updated"] = int(time.time())
		# Write the metadata object
		SetMetadata(metadata)
	# Returns the Reddit CSS %%variable%%
	return "%%header" + str(metadata["header_cycle"]["current_index"] + 1) + "%%"