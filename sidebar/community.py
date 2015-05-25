import json
import time
import datetime
import sys
import traceback
import os, sys
import logging
import io
from settings import getSettings

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Builds the entire community section
def BuildCommunitySection():
	markdown = ">>Community\n\n[ ](#db)  \n\n"
	markdown += BuildCommunitySpotlight()
	markdown += BuildWorkshopSpotlight()
	markdown += ">>[ ](#db) \n\n"
	markdown += ">>>[Steam Forums](http://forums.steampowered.com/forums/forumdisplay.php?f=1188)\n\n"
	markdown += ">>[ ](#db) \n\n>>>[Reddit Steam Group!](http://steamcommunity.com/groups/redditcommunitynights#RDDT)\n\n"
	markdown += ">>[ ](#db) \n\n\n"
	markdown += ">[ ](#goup) \n\n\n"
	markdown += "[ ](#db)  \n\n\n\n"
	return markdown

# Determines which community spotlight needs to be returned and updates it in the metadata file.
def GetCommunitySpotlight():
	botSettings = getSettings()
	metadata = json.loads(open(base_path + "cache/metadata.json", 'r').read())
	community_spotlight = json.loads(open(base_path + "config/community.json", 'r').read())["community_spotlight"]
	# if it's been at least 30 minutes since the last update...
	if int(time.time()) - int(metadata["community_spotlight_cycle"]["last_updated"]) > 60 * botSettings['spotlight_rotation_timeout']:
		# Long line, basically grabs the new index for the active spotlight
		metadata["community_spotlight_cycle"]["current_index"] = (metadata["community_spotlight_cycle"]["current_index"] + 1) % len(community_spotlight)
		metadata["community_spotlight_cycle"]["last_updated"] = int(time.time())
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	return community_spotlight[metadata["community_spotlight_cycle"]["current_index"]]

# This is the whole community section, the workshop bit will have to be added later on or merged with the community spotlight rotation.
def BuildCommunitySpotlight():
	print("\tBuilding \"Community Spotlight\" section...")
	currentTime2 = datetime.datetime.now()
	spotlight = GetCommunitySpotlight()
	markdown = ">>>#[SPOTLIGHT:](" + spotlight["link"] + "#ytspotlight)  \n"
	markdown += "##" + spotlight["title"] + "\n"
	markdown += "###" + spotlight["description"] + "\n\n\n"
	print("\tBuild of \"Community Spotlight\" section complete (" + str(len(markdown)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
	return markdown

# Grabs workshop spotlight data from the metadata file and community file to get the correct part
def GetWorkshopSpotlight():
	botSettings = getSettings()
	metadata = json.loads(open(base_path + "cache/metadata.json", 'r').read())
	workshop_spotlight = json.loads(open(base_path + "config/community.json", 'r').read())["workshop_spotlight"]
	# if it's been at least 30 minutes since the last update...
	if int(time.time()) - int(metadata["workshop_spotlight_cycle"]["last_updated"]) > 60 * botSettings['spotlight_rotation_timeout']:
		# Long line, basically grabs the new index for the active spotlight
		metadata["workshop_spotlight_cycle"]["current_index"] = (metadata["workshop_spotlight_cycle"]["current_index"] + 1) % len(workshop_spotlight)
		metadata["workshop_spotlight_cycle"]["last_updated"] = int(time.time())
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	return workshop_spotlight[metadata["workshop_spotlight_cycle"]["current_index"]]

# Constructs the Workshop Spotlight section based on the correct target workshop item
def BuildWorkshopSpotlight():
	print("\tBuilding \"Workshop Spotlight\" section...")
	currentTime2 = datetime.datetime.now()
	spotlight = GetWorkshopSpotlight()
	markdown = ">>>#[WORKSHOP:](" + spotlight["link"] + "&searchtext=?workshop)\n"
	markdown += "##" + spotlight["title"] + "\n"
	markdown += "###" + spotlight["description"] + "\n\n\n"
	print("\tBuild of \"Workshop Spotlight\" section complete (" + str(len(markdown)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
	return markdown