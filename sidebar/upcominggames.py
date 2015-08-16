import json
import time
import datetime
import urllib2
import sys
import traceback
import os, sys
import logging
import io
from settings import getSettings

logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	
gosucache = {}
gosucache["matches"] = []
base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

from helperfuncs import GetShortenedUrl,GetTimeUntilGameStart
import reddit

# Generates markdown for displaying upcoming games in the sidebar
def BuildUpcomingGames(upcomingGames):
	print("\tBuilding \"Upcoming Games\" section...")
	currentTime2 = datetime.datetime.now()
	markdown = '[*Match Ticker*](#heading)'
	i = 0
	for game in upcomingGames:
		markdown += "\n\n>>>\n[~~" + game['tournament'] + "~~\n"
		markdown += "~~" + game['timeleft'] + "~~\n"
		markdown += "~~" + game['team1']['name'] + "~~\n"
		markdown += "~~" + game['team2']['name'] + "~~](" + game['link'] + "#info)\n"
		markdown += "[ ](#lang-" + game['team1']['cc'] + ")\n"
		markdown += "[ ](#lang-" + game['team2']['cc'] + ")\n"
		if (upcomingGames.index(game) != len(upcomingGames) - 1):
			markdown += "\n>>[](#separator)"
	markdown += '\n\n**[See all](http://bit.ly/1xGEuiJ#button#slim)**\n\n'
	print("\tBuild of \"Upcoming Games\" section complete (" + str(len(markdown)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ", str(dt.total_seconds()), "s"
	return markdown

def GetUpcomingGames():
	print("\tRetrieving GosuGamers API data...")
	global gosucache
	botSettings = getSettings()
	count = reddit.count
	currentTime2 = datetime.datetime.now()
	v = None # object with upcoming games info from GosuGamers API
	gosuapi = "http://www.gosugamers.net/api/matches?apiKey=" + botSettings["gosugamers_api_key"] + "&game=counterstrike"
	upcomingGames = []
	try:
		try:
			gosusoup = urllib2.urlopen(urllib2.Request(gosuapi, headers={ 'User-Agent': 'Mozilla/5.0' })).read()
		except Exception as detail:
			# Do the error logging
			print "runtime error in GetUpcomingGames fetching from GosuGamers API: ", detail
			print "URL Attempted: " + gosuapi
			traceback.print_exc(file=sys.stdout)
			logging.exception("runtime error in GetUpcomingGames() fetching from GosuGamers API iter=" + str(count) + " :")
			# Return a cached copy of the data
			cachedData = json.loads(open(base_path + "cache/upcominggames.json", 'r').read())
			print("\tGosuGamers API data retrieval complete (using a cached copy)")
			return cachedData
		gosucache = json.loads(gosusoup)
		v = gosucache["matches"]
		dtnow = datetime.datetime.now()
		# Loop through a max of eight matches, minimum of zero
		#print ">>count",str(count)
		for index in range(0, botSettings['max_games_shown'] if len(v) > botSettings['max_games_shown'] else len(v)):
			try:
				# Team objects, name + two letter country code (US, CA, PL, SE, etc.)
				Team1 = {"name":str(v[index]["firstOpponent"]["shortName"]), "cc":str(v[index]["firstOpponent"]["country"]["countryCode"]).lower()}
				Team2 = {"name":str(v[index]["secondOpponent"]["shortName"]), "cc":str(v[index]["secondOpponent"]["country"]["countryCode"]).lower()}
				Tournament = v[index]["tournament"]["name"][:15] + "..." if len(v[index]["tournament"]["name"]) > 15 else v[index]["tournament"]["name"]
				Timeleft = GetTimeUntilGameStart(v[index]["datetime"])
				URL = GetShortenedUrl(v[index]["pageUrl"])
				IsLive = v[index]["isLive"]
				Maps = v[index]["maps"]
				# Fill up the object with all the data we need
				upcomingGames.append({"team1":Team1, "team2":Team2, "tournament":Tournament, "timeleft":Timeleft, "link":URL, "isLive":bool(IsLive), "maps":Maps})
			except Exception as detail:
				print "runtime error in GetUpcomingGames() trying to use gosucache :", detail
				traceback.print_exc(file=sys.stdout)
				logging.exception('runtime error in GetUpcomingGames() trying to use gosucache iter='+ str(count) +' :')
		print("\tGosuGamers API data retrieval complete")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		# Save this as a cached copy
		with io.open(base_path + "cache/upcominggames.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(upcomingGames, ensure_ascii=False)))
		return upcomingGames
	except Exception as detail:
		print "runtime error in GetUpcomingGames() getting gosucache :", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception('runtime error in GetUpcomingGames() getting gosucache iter='+ str(count) +' :')
		return upcomingGames