import datetime
import io
import json
import os
import praw
import random
import re
import time
import urllib2

base_path = os.getcwd()
settings = json.loads(open(base_path + "/settings.json", 'r').read())
items = json.loads(open(base_path + "/items.json", 'r').read())
redditAndSteamProfileDict = {}
startTime = datetime.datetime.now()

def getProfileLink(link):
	try:
		return re.findall('((https?:\/\/)?(www\.)?steamcommunity.com\/(id\/[\w-]+|profiles\/\d{17,18})(\/)?)', link, re.IGNORECASE)[0][0]
	except Exception as detail:
		return None

def getTradeOfferLink(link):
	try:
		return re.findall('((https?:\/\/)?(www\.)?steamcommunity.com\/tradeoffer\/new\/\?partner=\d+&token=.+(\/)?)', link, re.IGNORECASE)[0][0]
	except Exception as detail:
		return None

def isValidProfileLink(link):
	matches = re.match('(^|\A)((https?:\/\/)?(www\.)?steamcommunity.com\/(id\/[\w-]+|profiles\/\d{17,18})(\/)?)($|\z)', link, re.IGNORECASE)
	return len(matches.groups()) > 0 if matches != None else False

def isValidSteamId64(steamId):
	matches = re.match('(^|\A)\d{17,18}($|\z)', steamId, re.IGNORECASE)
	return len(matches.groups()) > 0 if matches != None else False

def isValidVanityId(vanityId):
	matches = re.match('(^|\A)[\w-]+?($|\z)', vanityId, re.IGNORECASE)
	return len(matches.groups()) > 0 if matches != None else False

problemVanityIds = []

def GetSteamId64FromVanity(vanityId):
	print("\t\tConverting vanity ID \"" + vanityId + "\" to Steam ID 64...")
	tries = 0
	while True:
		errorOccurred = False
		try:
			tries += 1
			response = json.loads(urllib2.urlopen("http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=" + settings['steam_api_key'] + "&vanityurl=" + vanityId, None, 10).read())['response']['steamid']
		except urllib2.URLError as detail:
			print("\t\t    -> Exception caught resolving vanity ID, retrying in three seconds...")
			errorOccurred = True
		except KeyError:
			print("\t\t    -> Exception caught, invalid vanity ID.")
			errorOccurred = True
		if errorOccurred == False:
			break
		if tries >= 5:
			print("\t\t    -> Five strikes, skipping this ID.")
			response = vanityId
			problemVanityIds.append(vanityId)
			break
		time.sleep(3) # Give the Steam API some time before retrying

	if isValidSteamId64(response):
		print("\t\t    -> Converted to " + response)
		return response
	else:
		print("\t\t    -> Could not be converted.")
		return None

def getRedditUsernameFromSteamProfileLink(profileLink):
	return redditAndSteamProfileDict[getSteamIdFromProfileLink(profileLink).lower()]

def getSteamIdFromProfileLink(profileLink):
	# Get the Steam ID or Vanity ID from the profile URL
	exploded = profileLink.split('/')
	steamId = exploded[len(exploded) - 1]
	# Handle if there's a slash at the end of the URL, probably a poor way to do this
	if not isValidSteamId64(steamId) and not isValidVanityId(steamId):
		steamId = exploded[len(exploded) - 2]
	return steamId

print("Logging in...")

# Log into the moderator account.
user_agent = ("RedditSteamUrlGrabber 1.0 by /u/Jpon9")
r = praw.Reddit(user_agent=user_agent)
r.login(settings['bot']['username'], settings['bot']['password'])

print("Logged in!")
print("Getting thread...")

threadId = settings['thread_id']

# Get the thread comments in a flat list.
thread = r.get_submission(submission_id=threadId)
thread.replace_more_comments(limit=None, threshold=0)
thread = praw.helpers.flatten_tree(thread.comments)
print("Thread retrieved!")
print ("Grabbing entries...")

entries = []
authors = []
steamIds = []
invalid = []

# Grab all the Steam profile links from the thread
for comment in thread:
	# Ignore removed comments
	if comment.banned_by != None:
		continue
	profileLink = getProfileLink(comment.body)
	if profileLink != None and isValidProfileLink(profileLink) and str(comment.author) not in authors:
		entry = {}
		entry['author'] = str(comment.author)
		entry['profile'] = profileLink
		entry['body'] = comment.body
		steamId = getSteamIdFromProfileLink(profileLink)
		# Make sure the ID we're storing is a Steam Community ID, otherwise don't store it at all
		if isValidSteamId64(steamId):
			entry['steamid'] = steamId
		elif isValidVanityId(steamId):
			entry['steamid'] = GetSteamId64FromVanity(steamId)
		if entry['steamid'] == None or entry['steamid'] in steamIds:
			continue
		# Add the entry and the author/IDs so we don't get duplicates
		authors.append(entry['author'])
		steamIds.append(entry['steamid'])
		entries.append(entry)
		redditAndSteamProfileDict[entry['steamid']] = entry['author']
		print("\tUser \"" + entry['author'] + "\" added with Steam ID \"" + entry['steamid'] + "\"")
	else:
		invalid.append({"author":str(comment.author),"id":comment.id,"body":comment.body})

print("All entries grabbed.")
print("Checking account ages...")

batches = [[]]
batchSize = 50

for steamId in steamIds:
	if len(batches[len(batches) - 1]) >= batchSize:
		batches.append([])
	batches[len(batches) - 1].append(steamId)

profileSummaries = []

batchesFetched = 0
for batch in batches:
	tries = 0
	batchesFetched += 1
	print("\tFetching Batch #" + str(batchesFetched) + "...")
	while True:
		errorOccurred = False
		try:
			tries += 1
			summaries = json.loads(urllib2.urlopen("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" + settings['steam_api_key'] + "&steamids=" + ",".join(batch), None, 10).read())['response']['players']
		except urllib2.URLError as detail:
			print("\t\t    -> Exception caught grabbing profile summaries, retrying in four seconds...")
			errorOccurred = True
		if errorOccurred == False:
			break
		if tries >= 10:
			print("\t\t    -> Ten strikes, skipping this batch.")
			break
		time.sleep(4) # Give the Steam API some time before retrying
	print("\tBatch #" + str(batchesFetched) + " has been fetched.")
	profileSummaries += summaries

print("All batches have been fetched.")
print("There are " + str(len(profileSummaries)) + " entries.")
print("Purging accounts made after December 5th...")

acceptedEntries = []
privateProfiles = []

for summary in profileSummaries:
	if 'timecreated' not in summary:
		privateProfiles.append(summary['steamid'])
		continue
	if summary['timecreated'] <= 1417737600: # December 5th, GMT
		acceptedEntries.append(summary['steamid'])

print("There are now " + str(len(acceptedEntries)) + " entries.")
print("Dumping valid Steam profiles into valid-entries.json.")
print("\nNow choosing winners...")

winners = []
inventory = []
random.seed()
numOfItemsGiven = 0
stillEligibleEntries = list(acceptedEntries)

for item, quantity in items.iteritems():
	for i in range(0, quantity):
		inventory.append(item)

for item in inventory:
	if numOfItemsGiven >= len(acceptedEntries):
		print(str(numOfItemsGiven) + " items were given away, " + str(len(inventory) - numOfItemsGiven) + " are left.")
		break

	winner = random.choice(stillEligibleEntries)
	stillEligibleEntries.remove(winner)
	winnerObj = {}
	winnerObj['steam_profile'] = "http://steamcommunity.com/profiles/" + str(winner)
	winnerObj['reddit_username'] = getRedditUsernameFromSteamProfileLink(winner)
	winnerObj['item'] = item
	winners.append(winnerObj)
	print("\t" + winnerObj['reddit_username'] + " | " + item)
	numOfItemsGiven += 1

print("Done! (Duration: " + "{0:.2f}".format((datetime.datetime.now() - startTime).total_seconds()) + "s)\n")
print(str(numOfItemsGiven) + " of " + str(len(inventory)) + " have been assigned. (" + "{0:.2f}".format(numOfItemsGiven / len(inventory) * 100) + "%)")

# ===========================================
# All the debug info a man could ever want
# ===========================================

# Write the Reddit/Steam profile dictionary to a JSON file for storage/debugging
with io.open(base_path + "/cache/private-profiles.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(privateProfiles, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the Reddit/Steam profile dictionary to a JSON file for storage/debugging
with io.open(base_path + "/cache/reddit-steam-dict.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(redditAndSteamProfileDict, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the invalid vanity URLs to a JSON file for storage/debugging
with io.open(base_path + "/cache/problem-vanity-ids.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(problemVanityIds, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the valid profile URLs to a JSON file for storage/debugging
with io.open(base_path + "/cache/valid-entries.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(acceptedEntries, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the profile summaries to a JSON file for storage/debugging
with io.open(base_path + "/cache/profile-summaries.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(profileSummaries, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the batches to a JSON file for storage/debugging
with io.open(base_path + "/cache/batches.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(batches, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the entries to a JSON file for storage/debugging
with io.open(base_path + "/cache/entries.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(entries, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the invalid entries to a JSON file for storage/debugging
with io.open(base_path + "/cache/invalid-comments.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(invalid, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the inventory to a JSON file for storage/debugging
with io.open(base_path + "/cache/inventory.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(inventory, ensure_ascii=False, indent=4, separators=(',', ': '))))

# Write the valid profile URLs to a JSON file for storage/debugging
with io.open(base_path + "/cache/winners.json", 'w', encoding='utf-8') as f:
	f.write(unicode(json.dumps(winners, ensure_ascii=False, indent=4, separators=(',', ': '))))