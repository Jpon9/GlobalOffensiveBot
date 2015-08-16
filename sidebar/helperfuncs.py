import json
import time
import datetime
import urllib2
import sys
import traceback
import os, sys
import io
import logging
from apiclient.discovery import build # Google's API client for building URL shortening calls
from settings import getSettings, base_path
from metadata import GetMetadata, SetMetadata

logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	
supported_maps = ['de_dust2','de_train','de_mirage','de_nuke','de_inferno']

# Returns a time between now and the given GosuGamer formatted startTime
# The return will match the format of /r/GlobalOffensive's
# typical ETA on a game, which is the most significant digit and its unit
# Examples:
#	- 3h or 3.5h
#	- 4d
#	- 32m
#	- 28s
#	- LIVE
def GetTimeUntilGameStart(startTime):
	# GosuGamers uses this datetime format
	format = "%Y-%m-%dT%H:%M:%S+00:00"
	now = datetime.datetime.utcnow()
	start = datetime.datetime.strptime(startTime, format)
	seconds = int((start - now).total_seconds())
	if seconds < 0:
		return "LIVE"
	# More than a day away...
	elif seconds > 60*60*24:
		if seconds < 60*60*24*10:   ##If  more than 10 days don't add decimal
			x=float("{0:.1f}".format(float(seconds) / (60*60*24)))
			x2=int(x)
			return str(x2+0.5 if x >= x2+0.5 else x2) + "d"
		else:
			return str(int(seconds / (60*60*24))) + "d"
	# More than an hour away...
	elif seconds > 60*60:
		if seconds < 60*60*10:		##If  more than 10 hours don't add decimal
			x=float("{0:.1f}".format(float(seconds) / (60*60)))
			x2=int(x)
			return str(x2+0.5 if x >= x2+0.5 else x2) + "h"
		else:
			return str(int(seconds / (60*60))) + "h"
	# More than a minute away...
	elif seconds > 60:
		return str(int(seconds / 60)) + "m"
	# Less than a minute away...
	else:
		return str(int(seconds)) + "s"

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

def GetMapName(maps):
	if len(maps) == 0:
		return "Unknown"
	elif maps[0] in supported_maps:
		return maps[0][3:]
	else:
		return "Unknown"
		
def GetMatchmakingStatus():
	botSettings = getSettings()
	status = '';
	try:
		soup = urllib2.urlopen("http://api.steampowered.com/ICSGOServers_730/GetGameServersStatus/v001/?key=" + botSettings["steam_api_key"]).read()
		status = json.loads(soup)['result']['matchmaking']['scheduler']
	except Exception as e:
		status = 'offline'

	if status == 'offline':
		return "OFFLINE"
	elif status == 'normal':
		return "ONLINE"
	else:
		return "Unknown (" + status + ")"

def reset():
	print "Restarting in 5 minutes..."
	botSettings = getSettings()
	time.sleep(60 * botSettings['update_timeout'])
	python = sys.executable
	os.execl(python, python, * sys.argv)

# Updates the metadata.json file with overall bot data for the webpanel to display
def updateMetadata(sidebarLen, stylesheetLen):
	# Get the new data
	updateFinished = int(time.time())
	sidebarError = {"active": True if sidebarLen > 5120 else False, "chars_over": sidebarLen - 5120}
	stylesheetError = {"active": True if stylesheetLen > 100000 else False, "chars_over": stylesheetLen - 100000}
	# Grab the current file so everything remains up-to-date
	metadata = GetMetadata()
	# Update old file with the new data
	metadata["last_update_completed"] = updateFinished
	metadata["error_status"]["sidebar_length"] = sidebarError
	metadata["error_status"]["stylesheet_length"] = stylesheetError
	# Write the new metadata object to the file neatly
	SetMetadata(metadata)

# Uses the Google API to shorten URLs
def GetShortenedUrl(longUrl):
	botSettings = getSettings()
	body = {"longUrl": longUrl}
	service = build('urlshortener', 'v1', developerKey=botSettings["google_api_key"])
	shortenedUrl = None
	try:
		shortenedUrl = service.url().insert(body=body,fields="id").execute()["id"]
	except Exception as e:
		print("\t\tERROR: Could not shorten long url: " + longUrl)
	return shortenedUrl if shortenedUrl != None else longUrl