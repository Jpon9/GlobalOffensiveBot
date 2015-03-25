import json
import time
import praw #thirdparty
import datetime
import urllib2
import sys
import traceback
import os
import logging

logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')
base_path = os.getcwd() + "/"
testimages= os.getcwd() + "/images/debug/"

from upcominggames import *
from helperfuncs import *
from redditlogin import r
import reddit
from settings import getSettings
from spritesheet_maker import GenerateSpritesheet

# Formats data from Twitch into Reddit markdown for use in the sidebar
def BuildCurrentlyStreaming(currentlyStreaming, upcomingGames):
	print("\tBuilding \"Currently Streaming\" section...")
	global r
	botSettings = getSettings()
	count = reddit.count
	currentTime2 = datetime.datetime.now()
	divider = "[ ](#db)"
	markdown = "[*Live Streams*](#heading)\n\n";
	image_urls = []
	if currentlyStreaming != []:
		for stream in currentlyStreaming:
			image_urls.append(stream["thumbnail"])
		spritesheetPath = GenerateSpritesheet(image_urls)
		try:
			# Upload spritesheet
			r.upload_image(botSettings['target_subreddit'], spritesheetPath, botSettings['stream_thumbnail_css_name'])
		except Exception as detail:
			print "runtime error in BuildCurrentlyStreaming uploading to reddit :", detail
			traceback.print_exc(file=sys.stdout)
			logging.exception('runtime error in BuildCurrentlyStreaming uploading to reddit iter='+ str(count) +' :')
			print "\nTrying again in 5 minutes.."
			time.sleep(60 * botSettings['update_timeout'])
			print "Trying..."
			try:
				r.upload_image(botSettings['target_subreddit'], spritesheetPath, botSettings['stream_thumbnail_css_name'])
			except Exception as detail:
				print "\tRuntime error |again| in BuildCurrentlyStreaming() :", detail
				traceback.print_exc(file=sys.stdout)
				logging.exception('Runtime error |again| in BuildCurrentlyStreaming() iter='+ str(count) +' :')
				print "\nRestarting script."
				reset()
		streamPosition = 0
		for stream in currentlyStreaming:
			markdown += ">>>#[" + stream['title'] + "](" + stream['url'] + "#profile-" + str(streamPosition) + ")\n##   \n### " + stream['viewers'] + " @ " + stream['streamer'] + "\n\n>>[](#separator)\n\n"
			streamPosition += 1
	markdown += "**[See all](http://bit.ly/1rXZ9v3#button#slim)**\n\n"
	print("\tBuild of \"Currently Streaming\" section complete (" + str(len(markdown)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
	return markdown
	
def GetCurrentlyStreaming():
	botSettings = getSettings()
	# Get the data from each stream source
	twitch = GetCurrentlyStreamingOnTwitch()
	mlg = GetCurrentlyStreamingOnMLG()
	hitbox = GetCurrentlyStreamingOnHitbox()
	azubu = GetCurrentlyStreamingOnAzubu()
	# Combine them into one object
	currentlyStreaming = twitch + mlg + hitbox + azubu
	# Sort them by viewers
	currentlyStreaming = sorted(currentlyStreaming, key=lambda channel: channel['viewers_raw'], reverse=True)
	# Trim off the remainder if there were more than the number we're supposed to use
	if len(currentlyStreaming) > botSettings['max_streams_shown']:
		currentlyStreaming = currentlyStreaming[:botSettings['max_streams_shown']]
	# Return the object for converting the data to markdown
	return currentlyStreaming

def GetCurrentlyStreamingOnTwitch():
	count = reddit.count
	print("\tRetrieving Twitch API data...")
	currentTime2=datetime.datetime.now()
	botSettings = getSettings()
	currentlyStreaming = [] # Holds five top CS:GO streams and some extra data about them
	try:
		twitchcache = {}
		try:
			twitchcache = urllib2.urlopen("https://api.twitch.tv/kraken/streams/?game=Counter-Strike:%20Global%20Offensive&limit=" + str(botSettings['max_streams_shown'])).read()
		except Exception as detail:
			# Do the error logging
			print "runtime error in GetCurrentlyStreamingOnTwitch fetching from Twitch API: " + detail
			print "URL Attempted: https://api.twitch.tv/kraken/streams/?game=Counter-Strike:%20Global%20Offensive&limit=" + str(botSettings['max_streams_shown'])
			traceback.print_exc(file=sys.stdout)
			logging.exception("runtime error in GetCurrentlyStreamingOnTwitch fetching from Twitch API iter=" + str(count) + " :")
			# Return a cached copy of the data
			cachedData = json.loads(open(base_path + "cache/currentlystreaming-twitch.json", 'r').read())
			print("\tTwitch API data retrieval complete (using a cached copy)")
			dt = datetime.datetime.now() - currentTime2
			print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
			return cachedData
		j = json.loads(twitchcache)
		for i, stream in enumerate(j["streams"]):
			# Fill up the currentlyStreaming object with top 5 Twitch CS:GO streams
			try:
				Streamer = stream["channel"]["display_name"]
				Title = SanitizeMarkdown(stream["channel"]["status"] if len(stream["channel"]["status"]) < 42 else stream["channel"]["status"][:42] + "...").strip()
				Link = stream["channel"]["url"]
				ViewersRaw = int(stream["viewers"])
				Viewers = '{:,}'.format(ViewersRaw)
				Thumbnail = stream["preview"]["template"].replace("{width}","45").replace("{height}","30")
				Language = stream["channel"]["language"]
				# Fill up Currently Streaming with data to be built into the sidebar
				currentlyStreaming.append({"streamer":Streamer,"title":Title,"url":Link,"viewers":Viewers,"viewers_raw":ViewersRaw,"thumbnail":Thumbnail,"language":Language})
			except Exception as detail:
				if "KeyError" not in str(detail):
					print ">>Exception in getting stream title -/-",detail,"-/-"
					traceback.print_exc(file=sys.stdout)
				#break
				cachedData = json.loads(open(base_path + "cache/currentlystreaming-twitch.json", 'r').read())
				# Write the Twitch return that gave us an error to disk
				with io.open(base_path + "cache/error/twitch-" + str(int(time.time())) + ".json", 'w', encoding='utf-8') as f:
					f.write(unicode(twitchcache))
				print("\tTwitch API data retrieval complete (using a cached copy)")
				dt = datetime.datetime.now() - currentTime2
				print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
				return cachedData

		print("\tTwitch API data retrieval complete")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		# Save this as a cached copy
		with io.open(base_path + "cache/currentlystreaming-twitch.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(currentlyStreaming, ensure_ascii=False)))
		return currentlyStreaming
	except Exception as detail:
		print "runtime error in GetCurrentlyStreamingOnTwitch getting twitchcache :", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception('runtime error in GetCurrentlyStreamingOnTwitch getting twitchcache iter='+ str(count) +' :')
		cachedData = json.loads(open(base_path + "cache/currentlystreaming-twitch.json", 'r').read())
		print("\tTwitch API data retrieval complete (using a cached copy)")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return cachedData

def GetCurrentlyStreamingOnMLG():
	count = reddit.count
	botSettings = getSettings()
	# mlg279 = playCEVO
	# mlg436 = moe
	# mlg396 = csgo (English)
	# mlg310 = 99damage (German)
	# mlg432 = gaminglive (French)
	# mlg46  = mlgtv1 (Russian)
	# mlg199 = mlg-brasil (Portuguese)
	# mlg4   = gfinity
	# MLG told me we will be picking from only a group of streams until they filter their streams by game
	# CS:GO's game_id is 13 (?)
	mlgChannelsToPull = ["mlg279", "mlg436", "mlg396", "mlg310", "mlg432", "mlg46", "mlg199", "mlg4"]
	mlgChannelsToPullStr = ""
	mlgChannels = {} # Holds the top MLG streams
	print("\tRetrieving MLG TV API data...")
	currentTime2 = datetime.datetime.now()
	botSettings = getSettings()
	try:
		for channel in mlgChannelsToPull:
			try:
				streamDataStr = urllib2.urlopen("http://streamapi.majorleaguegaming.com/service/streams/status/" + channel).read()
			except Exception as detail:
				# Do the error logging
				print "runtime error in GetCurrentlyStreamingOnTwitch fetching from MLG TV API: ", detail
				print("URL Attempted: http://streamapi.majorleaguegaming.com/service/streams/status/" + channel)
				traceback.print_exc(file=sys.stdout)
				logging.exception("runtime error in GetCurrentlyStreamingOnMLG fetching from MLG TV API iter=" + str(count) + " :")
				# Return a cached copy of the data
				cachedData = json.loads(open(base_path + "cache/currentlystreaming-mlg.json", 'r').read())
				print("\tMLG TV API data retrieval complete (using a cached copy)")
				dt = datetime.datetime.now() - currentTime2
				print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
				return cachedData
			streamData = json.loads(streamDataStr)['data']
			if (streamData['status'] == -1) or ('viewers' not in streamData):
				continue
			mlgChannelsToPullStr += str(streamData['channel_id']) + ","
			mlgChannels[streamData['channel_id']] = {}
			mlgChannels[streamData['channel_id']]['viewers'] = '{:,}'.format(streamData['viewers'])
			mlgChannels[streamData['channel_id']]['viewers_raw'] = int(streamData['viewers'])
		if mlgChannelsToPullStr != "":
			urlParams = mlgChannelsToPullStr[:-1]
			try:
				channelDataStr = urllib2.urlopen("http://www.majorleaguegaming.com/api/channels/all?ids=" + urlParams).read()
			except Exception as detail:
				print "runtime error in GetCurrentlyStreamingOnMLG fetching from MLG TV API: ", detail
				print("URL Attempted: http://www.majorleaguegaming.com/api/channels/all?ids=" + urlParams)
				traceback.print_exc(file=sys.stdout)
				logging.exception("runtime error in GetCurrentlyStreamingOnMLG fetching from MLG TV API iter=" + str(count) + " :")
				# Return a cached copy of the data
				cachedData = json.loads(open(base_path + "cache/currentlystreaming-mlg.json", 'r').read())
				print("\tMLG TV API data retrieval complete (using a cached copy)")
				dt = datetime.datetime.now() - currentTime2
				print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
				return cachedData
			channelData = json.loads(channelDataStr)['data']['items']
			for channel in channelData:
				mlgChannels[channel['id']]['streamer'] = channel['name']
				mlgChannels[channel['id']]['title'] = SanitizeMarkdown(channel['subtitle'] if len(channel['subtitle']) < 42 else channel['subtitle'][:42] + "...")
				mlgChannels[channel['id']]['url'] = channel['url']
				mlgChannels[channel['id']]['thumbnail'] = channel['image_16_9_small'] # 208 x 117 is always the size, I believe
				mlgChannels[channel['id']]['language'] = 'en'
			sortedChannels = sorted(mlgChannels.values(), key=lambda channel: channel['viewers_raw'], reverse=True)
			if len(sortedChannels) > botSettings['max_streams_shown']:
				sortedChannels = sortedChannels[:botSettings['max_streams_shown']]
			print("\tMLG TV API data retrieval complete")
			dt = datetime.datetime.now() - currentTime2
			print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
			with io.open(base_path + "cache/currentlystreaming-mlg.json", 'w', encoding='utf-8') as f:
				f.write(unicode(json.dumps(sortedChannels, ensure_ascii=False)))
			return sortedChannels
		print("\tMLG TV API data retrieval complete (no channels live)")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return []
	except Exception as detail:
		print "runtime error in GetCurrentlyStreamingOnMLG getting MLG TV data :", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception('runtime error in GetCurrentlyStreamingOnMLG getting MLG TV data iter='+ str(count) +' :')
		cachedData = json.loads(open(base_path + "cache/currentlystreaming-mlg.json", 'r').read())
		print("\tTwitch API data retrieval complete (using a cached copy)")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return cachedData

def GetCurrentlyStreamingOnHitbox():
	count = reddit.count
	botSettings = getSettings()
	hitboxChannels = [] # Holds the top Hitbox streams
	print("\tRetrieving Hitbox API data...")
	currentTime2 = datetime.datetime.now()
	botSettings = getSettings()
	# Retrieve live CS:GO channels
	try:
		try:
			hitboxStreamsStr = urllib2.urlopen("http://api.hitbox.tv/media/?game=427&live=1&limit=" + str(botSettings['max_streams_shown'])).read()
		except Exception as detail:
			print "runtime error in GetCurrentlyStreamingOnHitbox fetching from Hitbox API: ", detail
			print("URL Attempted: http://api.hitbox.tv/media/?game=427&live=1&limit=" + botSettings['max_streams_shown'])
			traceback.print_exc(file=sys.stdout)
			logging.exception("runtime error in GetCurrentlyStreamingOnHitbox fetching from Hitbox API iter=" + str(count) + " :")
			# Return a cached copy of the data
			cachedData = json.loads(open(base_path + "cache/currentlystreaming-hitbox.json", 'r').read())
			print("\tHitbox API data retrieval complete (using a cached copy)")
			dt = datetime.datetime.now() - currentTime2
			print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
			return cachedData
		hitboxStreams = json.loads(hitboxStreamsStr)['livestream']
		for stream in hitboxStreams:
			ViewersRaw = int(stream["media_views"])
			Viewers = '{:,}'.format(ViewersRaw)
			Streamer = stream["media_display_name"]
			Title = SanitizeMarkdown(stream["media_status"] if len(stream["media_status"]) < 42 else stream["media_status"][:42] + "...").strip()
			Link = stream["channel"]["channel_link"]
			Thumbnail = "http://edge.sf.hitbox.tv/" + stream["media_thumbnail"]
			Language = stream["media_countries"][0] if stream["media_countries"] != None and len(stream["media_countries"]) > 0 else "en"
			hitboxChannels.append({"streamer":Streamer,"title":Title,"url":Link,"viewers":Viewers,"viewers_raw":ViewersRaw,"thumbnail":Thumbnail,"language":Language})
		with io.open(base_path + "cache/currentlystreaming-hitbox.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(hitboxChannels, ensure_ascii=False)))
		print("\tHitbox API data retrieval complete")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return hitboxChannels
	except Exception as detail:
		print "runtime error in GetCurrentlyStreamingOnHitbox fetching from Hitbox API: ", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception("runtime error in GetCurrentlyStreamingOnHitbox fetching from Hitbox API iter=" + str(count) + " :")
		# Return a cached copy of the data
		cachedData = json.loads(open(base_path + "cache/currentlystreaming-hitbox.json", 'r').read())
		print("\tHitbox API data retrieval complete (using a cached copy)")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return cachedData

def GetCurrentlyStreamingOnAzubu():
	count = reddit.count
	botSettings = getSettings()
	azubuStreams = [] # Holds the top Azubu streams
	print("\tRetrieving Azubu API data...")
	currentTime2 = datetime.datetime.now()
	botSettings = getSettings()
	# Retrieve live CS:GO channels
	try:
		try:
			azubuStreamsStr = urllib2.urlopen("http://api.azubu.tv/public/channel/live/list/game/csgo?limit=" + str(botSettings['max_streams_shown'])).read()
		except Exception as detail:
			print "runtime error in GetCurrentlyStreamingOnAzubu fetching from Azubu API: ", detail
			print("URL Attempted: http://api.azubu.tv/public/channel/live/list/game/csgo?limit=" + str(botSettings['max_streams_shown']))
			traceback.print_exc(file=sys.stdout)
			logging.exception("runtime error in GetCurrentlyStreamingOnAzubu fetching from Azubu API iter=" + str(count) + " :")
			# Return a cached copy of the data
			cachedData = json.loads(open(base_path + "cache/currentlystreaming-azubu.json", 'r').read())
			print("\tAzubu API data retrieval complete (using a cached copy)")
			dt = datetime.datetime.now() - currentTime2
			print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
			return cachedData
		azubuChannels = json.loads(azubuStreamsStr)['data']
		for stream in azubuChannels:
			ViewersRaw = int(stream['view_count'])
			Viewers = '{:,}'.format(ViewersRaw)
			Streamer = stream['user']['display_name']
			Title = Streamer + " playing CS:GO"
			Link = stream['url_channel']
			Thumbnail = stream['url_thumbnail']
			Language = "en"
			azubuStreams.append({"streamer":Streamer,"title":Title,"url":Link,"viewers":Viewers,"viewers_raw":ViewersRaw,"thumbnail":Thumbnail,"language":Language})
		with io.open(base_path + "cache/currentlystreaming-azubu.json", 'w', encoding='utf-8') as f:
			f.write(unicode(json.dumps(azubuStreams, ensure_ascii=False)))
		print("\tAzubu API data retrieval complete")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return azubuStreams
	except Exception as detail:
		print "runtime error in GetCurrentlyStreamingOnAzubu fetching from Azubu API: ", detail
		traceback.print_exc(file=sys.stdout)
		logging.exception("runtime error in GetCurrentlyStreamingOnAzubu fetching from Azubu API iter=" + str(count) + " :")
		# Return a cached copy of the data
		cachedData = json.loads(open(base_path + "cache/currentlystreaming-azubu.json", 'r').read())
		print("\tAzubu API data retrieval complete (using a cached copy)")
		dt = datetime.datetime.now() - currentTime2
		print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
		return cachedData