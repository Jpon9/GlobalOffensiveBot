import json
import time
import datetime
import dateutil
from dateutil.relativedelta import *
from dateutil.tz import *
import sys
import traceback
import os
import logging
import io
import random

logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	
base_path = os.getcwd() + "/"

def GetNoticeList():
	#print("GetNoticeList() called")
	sticky = json.loads(open(base_path + "cache/stickies.json", 'r').read())
	notices=[]
	for post in sticky["stickies"]:
		if post["master_disable"] == False:
			if post['hide_notice'] == False:
				#if post["postedflag"] == 1:
				relativetime=dateutil.relativedelta.relativedelta(weekday=post["time"][0],hour=post["time"][1],minute=post["time"][2],second=0,microsecond=0)
				if (datetime.datetime.now(tzutc()) >= (datetime.datetime.now(tzutc()) + relativetime)) and \
					(datetime.datetime.now(tzutc()) <= (datetime.datetime.now(tzutc()) + relativetime + datetime.timedelta(hours=post["duration_hours"]))):
						
					notices.append(post)
					
				if "non_recurring" in post["type"]:
					if (datetime.datetime.now(tzutc()) > (datetime.datetime.now(tzutc()) + relativetime + datetime.timedelta(hours=post["duration_hours"]))):
						post["master_disable"] = True
						with io.open(base_path + "cache/stickies.json", 'w', encoding='utf-8') as f:
							f.write(unicode(json.dumps(sticky, ensure_ascii=False, indent=4, separators=(',', ': '))))
	
	if len(notices) > 3:
		random.shuffle(notices)
	return notices[:3]  #return no more than 3 notices 
	
def BuildNotices():
	print("\tBuilding \"Notices\" section...")
	currentTime2=datetime.datetime.now()
	noticelist=GetNoticeList()
	#template="\n\n1. [This is an event](#event)\n1. [This is a notice](#notice)\n1. [This is a discussion](#discussion)"
	
	markdown="\n"
	for notice in noticelist:
		markdown += "\n1. ["+notice["notice_title"]+"]("+notice["postlink"]+"#"+notice["noticetype"]+")"
	
	print("\tBuild of \"Notices\" section complete (" + str(len(markdown)) + " chars)")
	dt = datetime.datetime.now() - currentTime2
	print "\t\t-> Duration: ",str(dt.total_seconds()),"s"
	return markdown
