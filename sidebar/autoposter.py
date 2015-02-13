# Reddit weekly post scheduler.
import praw, time, datetime, dateutil, json, os, io, reddit

from settings import getSettings
from accounts import getAccounts

from dateutil.relativedelta import *
from dateutil.tz import *
from dateutil.parser import parse

user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)

subreddit = getSettings()["target_subreddit"]
account = ''
base_path = os.getcwd() + "/"

#Main method for autoposter. This method runs in a separate thread from the rest of the bot.
#Continuous loop that checks the stickies.json file for all Notices to be posted or removed.
def autoposterfunc():
    botAccounts = getAccounts()
    while True:
        sticky = reddit.sticky

        for post in sticky["stickies"]:
            #If post is disabled, skip.
            if post["master_disable"]:
                pass
            #If post is live and should come down, remove it.
            elif post["postedflag"] == 1: 
                #Boolean to remove post or not (True == remove post, False == don't remove post)
                toremove = checkremove(post)
                if toremove:
                    postlogin(post,botAccounts)
                    removepost(post,sticky)
                continue

            #Check time of unposted notices, submit post if it is time. 
            elif post["postedflag"] == 0: 
                #Boolean to post or not (True == post, False == don't post)
                topost = checkpost(post)
                if topost:
                    postlogin(post,botAccounts)
                    submitpost(post,sticky)
                else: 
                    pass

        time.sleep(1)

#Submethod of autoposterfunc() that checks the time a Notice is set to be posted.
#If the post is set at the current day, hour, and minute, the method returns True to continue posting the Notice.
def checkpost(post):
    postday = post["time"][0]
    posthour = post["time"][1]
    postminute = post["time"][2]

    now = datetime.datetime.now(tzutc())

    #If it is time to post the Notice, return True bool to main function
    if (now.weekday() == postday) and (now.hour == posthour) and (now.minute == postminute):
        return True
    else:
        return False

#Submethod of autoposterfunc() that checks the time a Notice is set to be removed.
#If the post is set to remove at the current day, hour, and minute, the method returns True to continue removing the Notice.
def checkremove(post):
    #Retrieve remove_time from stickies.json, and set time right now
    removal_time = parse(post["removal_time"])
    now = datetime.datetime.now(tzutc())

    #If it is time to remove this post, return True bool to main function
    if now > removal_time:
        return True
    else:
        return False

#Submethod of autoposterfunc() that performs the login to Reddit via PRAW.
#This method checks settings.json for which account to post a Notice under, then logs into Reddit as that user.
#Method returns to autoposterfunc() to continue processing the thread.
def postlogin(post,botAccounts):
    poster_accounts = reddit.poster_accounts

    #Set account to post Notice
    if post.has_key("poster_account"):
        if poster_accounts.has_key(post["poster_account"]):
            account = str(post["poster_account"])
        else:
            account = 'GlobalOffensiveBot'

    elif not post.has_key("poster_account"):
        account='GlobalOffensiveBot'
    
    #Log into account to be used for notice posting
    if str(r.user) != account:
        r.clear_authentication()
        try:
            if account != 'GlobalOffensiveBot':
                r.login(str(post["poster_account"]), str(poster_accounts[post["poster_account"]])) # username and password of poster
            else:
                r.login(botAccounts['__MODERATORACCOUNT__'], botAccounts[botAccounts['__MODERATORACCOUNT__']])
                
        except Exception, e:
            print "NOTICE ERROR: Unable to log into ",str(account),": %s" % str(e)
    return

#Submethod of autoposterfunc() that performs the Post to Reddit via PRAW.
#Extracts post data from settings.json and submits the post.
#Retrieves URL for Notice then places it into settings.json
#Returns to autoposterfunc() to continue processing the thread.
def submitpost(post,sticky):
    #Create removal time (now plus the duration of the post) to be stored in stickies.json and checked for removal
    remove_time = datetime.datetime.now(tzutc()) + datetime.timedelta(hours=post["duration_hours"])

    #Attempt to post the Notice via PRAW. Catch exception and possibly send modmail with any errors.
    try:
        postobj = r.submit(subreddit, post["post_title"], text=post["body"])  
        print "Post sent:",post["post_title"]

    except Exception, e:
        print "NOTICE ERROR: Failed to submit post: ",post["post_title"],": %s" % str(e)
        return

    #Mark post as live to be updated in stickies.json
    post["postedflag"] = 1
    time.sleep(2)

    #Retrieve link URL from post, save to stickies.json
    post["postlink"] = postobj.url
    post["removal_time"] = remove_time.isoformat()

    #Write changes to stickies.json
    try:
        with open(base_path + "cache/stickies.json", 'r+', encoding='utf-8') as f:
            json.dump(sticky,f,ensure_ascii=False, indent=4, separators=(',', ': '))))
    
    except Exception, e:
        print "NOTICE ERROR: Failed to write to sickies.json after post: ",post["post_title"],": %s" % str(e)

    return

#Submethod of autoposterfunc() that removes a Post from Reddit via PRAW.
#Sets Notice to not-posted in settings.json, and disables the post if non-recurring.
#Returns to autoposterfunc() to continue processing the thread.
def removepost(post,sticky):
    #Try to remove post, log error if fail.
    try:
        submission = r.get_submission(post["postlink"])
        submission.delete()
        print "Post successfully removed: ",post["post_title"]

    except Exception, e:
        print "NOTICE ERROR: Unable to remove post: ",post["post_title"],": %s" % str(e)
        return

    #Revert posting flag to not-posted
    post["postedflag"] = 0

    #If post is one-time, disable the post
    if "non_recurring" in post["type"]:
        post["master_disable"] = True

    #Write changes to stickies.json
    try:
        with open(base_path + "cache/stickies.json", 'r+', encoding='utf-8') as f:
            json.dump(sticky,f,ensure_ascii=False, indent=4, separators=(',', ': '))))

    except Exception, e:
        print "NOTICE ERROR: Failed to write to sickies.json after remove: ",post["post_title"],": %s" % str(e)

    return