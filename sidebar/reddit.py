import json
import time
import praw #thirdparty
import datetime
import urllib2
import sys
import traceback
import os
import logging
from cssmin import cssmin # thirdparty, pip install cssmin
from settings import getSettings,refreshSettings
from accounts import getAccounts,refreshAccounts

logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	
base_path = os.getcwd() + "/"
count = 0
sticky = {}
sticky["stickies"] = []
poster_accounts={}

from community import BuildCommunitySection
from demonym_cycle import BuildDemonymRule
from streaming import GetCurrentlyStreaming,BuildCurrentlyStreaming
from upcominggames import GetUpcomingGames,BuildUpcomingGames
from helperfuncs import GetMatchmakingStatus,reset,updateMetadata
from redditlogin import r
from notices import BuildNotices
from header_rotation import GetHeader

# Builds the sidebar and writes it to description.txt after retrieving necessary API data.
def BuildSidebar():
	currentTime2 = datetime.datetime.now()
	sidebarJson = json.loads(open(base_path + "config/description.json", 'r').read())
	sidebar = sidebarJson['template']
	currentlyStreaming = GetCurrentlyStreaming()
	upcomingGames = GetUpcomingGames()
	for chunk in sidebarJson['chunks']:
		# Dynamic sections
		if chunk['name'] == 'currently_streaming':
			chunk['body'] = BuildCurrentlyStreaming(currentlyStreaming, upcomingGames)
		elif chunk['name'] == 'upcoming_games':
			chunk['body'] = BuildUpcomingGames(upcomingGames)
		elif chunk['name'] == 'community':
			chunk['body'] = BuildCommunitySection()
		elif chunk['name'] == 'resources':
			chunk['body'] = chunk['body'].replace("{MM_STATUS}", GetMatchmakingStatus())
			chunk['body'] = chunk['body'].replace("{MM_STATUS_TAG}", "#mm-up" if GetMatchmakingStatus() == "ONLINE" else "#mm-down")
		elif chunk['name'] == 'notices':
			chunk['body'] = BuildNotices()
		sidebar = sidebar.replace(chunk['name'], "\n\n" + chunk['body'])

	description = open(base_path + "config/description.txt", 'w')
	description.write(sidebar.encode('utf-8'))
	description.close()

def BuildStylesheet():
	currentTime2 = datetime.datetime.now()
	botSettings = getSettings()
	print("\tBeginning stylesheet update...")
	base_stylesheet = open(base_path + "/config/stylesheet/main_stylesheet.txt", 'r').read().replace('%%header%%', GetHeader())
	stylesheetRules = BuildDemonymRule() + "\n\n" + base_stylesheet
	
	stylesheet = open(base_path + "config/stylesheet.txt", 'w')
	minifiedStylesheet = ""
	if botSettings['minify_stylesheet'] == True:
		minifiedStylesheet = cssmin(stylesheetRules.encode('utf-8'))
	else:
		minifiedStylesheet = stylesheetRules.encode('utf-8')
	stylesheet.write(minifiedStylesheet)
	stylesheet.close()
	stylesheet_unminified = open(base_path + "config/stylesheet_unminified.txt", 'w')
	stylesheet_unminified.write(stylesheetRules.encode('utf-8'))
	stylesheet_unminified.close()
	print("\tStylesheet update complete (" + str(len(minifiedStylesheet)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ",str(dt.total_seconds()),"s"

def main():
	global r
	global count
	botSettings = getSettings()
	botAccounts = getAccounts()
	try:
		currentTime = ""
		while True:
			refreshSettings()
			botSettings = getSettings()
			currentTime = datetime.datetime.now().strftime("%B %d, %Y at %I:%M:%S %p")
			currentTime2 = datetime.datetime.now()
			print("Starting a new sidebar update (" + currentTime + ")")
			loadposts()
			# loadposts is commented out because it is not fully completed
			# and no incomplete part of the bot should run on /r/GlobalOffensive
			# due to the risky nature of doing so.

			# Build the sidebar from its constituent parts
			BuildSidebar()
			BuildStylesheet()
			# Get the new sidebar markdown
			sidebarMarkdown = open(base_path + 'config/description.txt', 'r').read()
			sidebarLength = len("Last Updated " + currentTime + "\n\n" + sidebarMarkdown)
			# Get the new stylesheet
			license = open(base_path + 'config/stylesheet/license.txt').read()
			stylesheet = license + "\n\n" + open(base_path + 'config/stylesheet.txt', 'r').read()
			stylesheetLength = len(stylesheet)
			# Send error message if markdown too long
			# TODO: Use the logger to send error PMs for these
			if sidebarLength > 5120:
				print("WARNING! Sidebar markdown has too many characters and will NOT update! (" + str(sidebarLength - 5120) + " chars too long)")
				time.sleep(60 * botSettings['update_timeout'])
				continue
			elif stylesheetLength > 100000:
				print("WARNING! Stylesheet has too many characters and will NOT update! (" + str(stylesheetLength - 100000) + " chars too long)")
				time.sleep(60 * botSettings['update_timeout'])
				continue
			print("\tUploading sidebar...")
			lastUpdatedStr = ""
			#lastUpdatedStr = "Last Updated " + currentTime + "\n\n"
			r.update_settings(
				subreddit=botSettings['target_subreddit'],
				description=lastUpdatedStr + sidebarMarkdown)
			print("\tUploading stylesheet...")
			r.set_stylesheet(botSettings['target_subreddit'], stylesheet)
			print("Sidebar update #" + str(count) + " complete (" + str(sidebarLength) + " chars used, " + str(5120 - sidebarLength) + " left)")
			count += 1
			dt = datetime.datetime.now() - currentTime2
			print "End Time: ",str(datetime.datetime.now().strftime("%B %d, %Y at %I:%M:%S %p")),"\nTotal Duration: ",str(dt.total_seconds()),"s\n"
			updateMetadata(sidebarLength, stylesheetLength)
			time.sleep(60 * botSettings['update_timeout'])
	except Exception as detail:
		print "runtime error in main() :", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception('runtime error in main() iter='+ str(count) +' :')
		try:
			pass
			#r.send_message("Jpon9", "GlobalOffensiveBot Exception", str("runtime error in main() :\n"+ str(detail) +"\n\n"+ str(traceback.print_exc(file=sys.stdout))))
		except Exception as detail:
			print "error sending exception reddit message :", detail
			traceback.print_exc(file=sys.stdout)
			logging.exception('error sending exception reddit message iter='+ str(count) +' :')
		time.sleep(60 * botSettings['update_timeout'])
		try:
			user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
			r = praw.Reddit(user_agent=user_agent)
			r.login(botAccounts['__MODERATORACCOUNT__'], botAccounts[botAccounts['__MODERATORACCOUNT__']])
			botIsModerator = False
			for subreddit in r.get_my_moderation():
				if subreddit.display_name.lower() == botSettings['target_subreddit'].display_name.lower():
					botIsModerator = True
					break
			if botIsModerator == False:
				print "ERROR: Bot account '" + botAccounts['__MODERATORACCOUNT__'] + "' is not a mod on /r/" + botSettings['target_subreddit'].display_name
				print botAccounts['__MODERATORACCOUNT__'] + " is a mod in:\n" + '\n'.join([str(x) for x in r.get_my_moderation()])
				sys.exit(1)
			try:
				BuildSidebar()
				sidebarMarkdown = open(base_path + 'config/description.txt', 'r').read()
				r.update_settings(botSettings['target_subreddit'], description=sidebarMarkdown)
				print "Handled error, update complete."
				time.sleep(60 * botSettings['update_timeout'])
				main()
			except Exception as detail:
				print "error updating :",detail
				traceback.print_exc(file=sys.stdout)
				logging.exception('error updating iter='+ str(count) +' :')
				print "\nRestarting script..."
				reset()
		except Exception as detail:
			print "error logging into reddit :",detail
			traceback.print_exc(file=sys.stdout)
			logging.exception('error logging into reddit iter='+ str(count) +' :')
			print "\nRestarting script..."
			reset()
			
def loadposts():
	global sticky
	global poster_accounts
	poster_accounts = json.loads(open(base_path + "config/accounts.json", 'r').read())['accounts']
	sticky = json.loads(open(base_path + "cache/stickies.json", 'r').read())
