<<<<<<< HEAD
import json
import time
import datetime
import sys
import traceback
import os, sys
import logging
import io
from metadata import GetMetadata, SetMetadata

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Updates the "Users online..." and "Subscribers..." fields on the sidebar via the CSS
def GetDemonym():
	metadata = GetMetadata()
	demonyms = json.loads(open(base_path + "config/stylesheet/random_demonyms.json", 'r').read())["demonyms"]
	# if it's been at least 30 minutes since the last update...
	if metadata["demonym_cycle"]["current_index"] > len(demonyms):
		metadata["demonym_cycle"]["current_index"] = 0
		SetMetadata(metadata)
	if len(demonyms) == 0:
		demonyms = [{"subscribers":"subsribers","online":"users online"}]
		with io.open(base_path + "config/stylesheet/random_demonyms.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps({"demonyms":demonyms}, ensure_ascii=False, indent=4, separators=(',', ': '))))
	if int(time.time()) - int(metadata["demonym_cycle"]["last_updated"]) > 60 * 30:
		# Grabs the new index for the active demonym
		metadata["demonym_cycle"]["current_index"] = (metadata["demonym_cycle"]["current_index"] + 1) % len(demonyms)
		metadata["demonym_cycle"]["last_updated"] = int(time.time())
		SetMetadata(metadata)
	return demonyms[metadata["demonym_cycle"]["current_index"]]

# Returns the CSS rules to prepend/append to the stylesheet in order to update them
def BuildDemonymRule():
	print("\t\tBuilding demonym rules...")
	currentTime2 = datetime.datetime.now()
	demonym = GetDemonym()
	style = ".subscribers .number:after {\n"
	style += "\tcontent: \" " + demonym["subscribers"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".users-online .number:after {\n"
	style += "\tcontent: \" " + demonym["online"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".res-nightmode .subscribers .number:after {\n"
	style += "\tcontent: \" " + demonym["subscribers"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".res-nightmode .users-online .number:after {\n"
	style += "\tcontent: \" " + demonym["online"].replace("\"", "''") + "\";"
	style += "}\n\n"
	print("\t\tBuild of demonym rules complete (" + str(len(style)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t\t-> Duration: ",str(dt.total_seconds()),"s"

=======
import json
import time
import datetime
import sys
import traceback
import os, sys
import logging
import io

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Updates the "Users online..." and "Subscribers..." fields on the sidebar via the CSS
def GetDemonym():
	metadata = json.loads(open(base_path + "cache/metadata.json", 'r').read())
	demonyms = json.loads(open(base_path + "config/stylesheet/random_demonyms.json", 'r').read())["demonyms"]
	# if it's been at least 30 minutes since the last update...
	if metadata["demonym_cycle"]["current_index"] > len(demonyms):
		metadata["demonym_cycle"]["current_index"] = 0
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	if len(demonyms) == 0:
		demonyms = [{"subscribers":"subsribers","online":"users online"}]
		with io.open(base_path + "config/stylesheet/random_demonyms.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps({"demonyms":demonyms}, ensure_ascii=False, indent=4, separators=(',', ': '))))
	if int(time.time()) - int(metadata["demonym_cycle"]["last_updated"]) > 60 * 30:
		# Grabs the new index for the active demonym
		metadata["demonym_cycle"]["current_index"] = (metadata["demonym_cycle"]["current_index"] + 1) % len(demonyms)
		metadata["demonym_cycle"]["last_updated"] = int(time.time())
		with io.open(base_path + "cache/metadata.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(metadata, ensure_ascii=False, indent=4, separators=(',', ': '))))
	return demonyms[metadata["demonym_cycle"]["current_index"]]

# Returns the CSS rules to prepend/append to the stylesheet in order to update them
def BuildDemonymRule():
	print("\t\tBuilding demonym rules...")
	currentTime2 = datetime.datetime.now()
	demonym = GetDemonym()
	style = ".subscribers .number:after {\n"
	style += "\tcontent: \" " + demonym["subscribers"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".users-online .number:after {\n"
	style += "\tcontent: \" " + demonym["online"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".res-nightmode .subscribers .number:after {\n"
	style += "\tcontent: \" " + demonym["subscribers"].replace("\"", "''") + "\";"
	style += "}\n\n"
	style += ".res-nightmode .users-online .number:after {\n"
	style += "\tcontent: \" " + demonym["online"].replace("\"", "''") + "\";"
	style += "}\n\n"
	print("\t\tBuild of demonym rules complete (" + str(len(style)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t\t-> Duration: ",str(dt.total_seconds()),"s"

>>>>>>> 9cac3ffc7ca44891b14144e22e1df4c1b1e14243
	return style