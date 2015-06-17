# Reddit weekly post scheduler.
import praw, time, datetime, dateutil, json, os, sys, io, reddit, random

from settings import getSettings, refreshSettings
from accounts import getAccounts, refreshAccounts

from dateutil.relativedelta import *
from dateutil.tz import *
from dateutil.parser import parse

user_agent = ("GlobalOffensiveBot 1.0 by /u/Jpon9 and /u/Tremaux")
r = praw.Reddit(user_agent=user_agent)

bot_accounts = getAccounts()
bot_settings = getSettings()
base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"
notice_path = base_path + "config/notices.json"

# Reference values
once_cooldown = 1
daily_cooldown = 60 * 60 * 24
weekly_cooldown = daily_cooldown * 7
biweekly_cooldown = weekly_cooldown * 2
monthly_cooldown = biweekly_cooldown * 2

notices = json.loads(open(notice_path, 'r').read())['notices']

def updateNotices():
    global notices
    notices = json.loads(open(notice_path, 'r').read())['notices']

# Main autoposter function
def autoposter():
    global notices
    lastNoticesUpdate = time.time()

    while True:
        refreshAccounts()
        refreshSettings()

        bot_accounts = getAccounts()
        bot_settings = getSettings()

        updateNotices()

        now = int(time.time())

        noticesToBuild = []

        for notice in notices:
            if notice['disable_posting'] == False:
                if 'autopost' in notice['type']:
                    # Get time into the week that the cycle should start
                    nDay = notice['post_time'][0] * 60 * 60 * 24
                    nHour = notice['post_time'][1] * 60 * 60
                    nMinute = notice['post_time'][2] * 60
                    frequency = getFreqCooldownFromStr(notice['frequency'])
                    # Determines when next to post
                    post_time = 0
                    if notice['last_posted'] != 0:
                        last_post_time = getUtcStartOfWeek(notice['last_posted']) + nDay + nHour + nMinute
                        post_time = last_post_time + frequency
                    else:
                        first_post_time = getUtcStartOfWeek(notice['created']) + nDay + nHour + nMinute
                        post_time = first_post_time
                        # If it's created after it should have been posted that day/week,
                        # wait till the next period it should be posted to begin the cycle
                        if notice['created'] > first_post_time:
                            if frequency > daily_cooldown:
                                post_time += weekly_cooldown
                            else:
                                post_time += daily_cooldown
                    last_posted_time = 0
                    if post_time > now:
                        last_posted_time = post_time - frequency
                    else:
                        last_posted_time = post_time
                    # Post if the time has passed // this time is automatically updated when it's posted,
                    # which solves the problem of potential duplicate posts
                    if post_time < now:
                        if notice['frequency'] != 'once' or notice['last_posted_id'] == '':
                            data = []
                            if not notice['self_post']:
                                # Link post
                                data = createLinkPost(notice['poster_account'], notice['thread_title'], notice['thread_link'])
                            else:
                                # Self post
                                sticky_duration = notice['sticky_duration'] if not notice['permanent_sticky'] else -1
                                data = createSelfPost(notice['poster_account'], notice['thread_title'], notice['body'], sticky_duration)
                            # Update last posted and last posted id
                            notice['last_posted'] = data[0]
                            notice['last_posted_id'] = data[1]
                            notice['is_stickied'] = data[2]
                    # If it's not permanent, has expired, and has record of the last posted id, unsticky it.
                    if notice['is_stickied'] and not notice['permanent_sticky'] and last_posted_time + notice['sticky_duration'] * 60 * 60 < now:
                        print "Unstickying " + notice['last_posted_id']
                        notice['is_stickied'] = False
                        r.login(bot_accounts['__MODERATORACCOUNT__'], bot_accounts[bot_accounts['__MODERATORACCOUNT__']])
                        r.get_submission(submission_id=notice['last_posted_id']).unsticky()

            if notice['hide_notice'] == False:
                if 'notice' in notice['type']:
                    # Get time into the week
                    nDay = notice['notice_start_time'][0] * 60 * 60 * 24
                    nHour = notice['notice_start_time'][1] * 60 * 60
                    nMinute = notice['notice_start_time'][2] * 60
                    frequency = getFreqCooldownFromStr(notice['frequency'])
                    # First time ever posted
                    first_start_time = getUtcStartOfWeek(notice['created']) + nDay + nHour + nMinute
                    if notice['created'] > first_start_time:
                            if frequency > daily_cooldown:
                                first_start_time += weekly_cooldown
                            else:
                                first_start_time += daily_cooldown
                    if notice['permanent_notice']:
                        if first_start_time < now:
                            if notice['type'] == 'autopost+notice':
                                noticesToBuild.append([notice['category'], notice['notice_title'], "http://redd.it/" + notice['last_posted_id']])
                            else:
                                noticesToBuild.append([notice['category'], notice['notice_title'], notice['notice_link']])
                            continue
                    # Determine when the notice should start
                    notice_start_time = 0
                    if notice['last_posted'] != 0:
                        last_post_time = getUtcStartOfWeek(notice['last_posted']) + nDay + nHour + nMinute
                        notice_start_time = last_post_time
                    else:
                        notice_start_time = first_start_time
                    # If we're in the scheduled start/stop time, put the notice up
                    if notice_start_time < now and now < notice_start_time + notice['notice_duration'] * 60 * 60:
                        if notice['type'] == 'autopost+notice':
                            noticesToBuild.append([notice['category'], notice['notice_title'], "http://redd.it/" + notice['last_posted_id']])
                        else:
                            noticesToBuild.append([notice['category'], notice['notice_title'], notice['notice_link']])

        # Write any changes to the notices to disk
        #saveJson(notice_path, {'notices': notices})
        saveNotices(notice_path, notices)
        # Write notices to notices section of the sidebar
        #print "Num notices to build: " + str(len(noticesToBuild))
        sidebarConfig = json.loads(open(base_path + "config/description.json", 'r').read())
        for chunk in sidebarConfig['chunks']:
            if chunk['name'] == '__notices__':
                chunk['body'] = ''
                if len(noticesToBuild) > 3:
                    random.shuffle(noticesToBuild)
                    noticesToBuild = noticesToBuild[:3]
                for notice in noticesToBuild:
                    chunk['body'] += "1. [" + notice[1] + "](" + notice[2] + "#" + notice[0] + ")\n"
                break
        saveJson(base_path + "config/description.json", sidebarConfig)
        saveJson(base_path + "cache/autoposter.json", { "last_updated": int(time.time()) })
        time.sleep(5)

def createLinkPost(account, title, link):
    print "SENDING LINK POST: " + title
    if account not in bot_accounts:
        print "ERROR: Account '" + account + "' is not in our system."
        return
    r.login(account, bot_accounts[account])
    thread = r.submit(subreddit=bot_settings['target_subreddit'], title=title, link=link)
    return [int(time.time()), thread.short_link.split('/')[3], False]

def createSelfPost(account, title, body, sticky_duration):
    print "SENDING SELF-POST: " + title
    if account not in bot_accounts:
        print "ERROR: Account '" + account + "' is not in our system."
        return
    r.login(account, bot_accounts[account])
    if sticky_duration != 0:
        botIsModerator = False
        for subreddit in r.get_my_moderation():
            if subreddit.display_name.lower() == bot_settings['target_subreddit'].display_name.lower():
                botIsModerator = True
                break
        if botIsModerator == False:
            print "ERROR: Account '" + account + "' is not a moderator and cannot post stickies"
            return
    thread = r.submit(subreddit=bot_settings['target_subreddit'], title=title, text=body, send_replies=False)
    if sticky_duration != 0:
        thread.sticky()
    print "Returning this as thread id: ", thread.short_link.split('/')[3]
    return [int(time.time()), thread.short_link.split('/')[3], sticky_duration != 0]
            

def getFreqCooldownFromStr(str):
    if str == 'once':
        return -1
    elif str == 'daily':
        return daily_cooldown
    elif str == 'weekly':
        return weekly_cooldown
    elif str == 'biweekly':
        return biweekly_cooldown
    elif str == 'monthly':
        return monthly_cooldown

def getUtcStartOfWeek(time):
    weekData = datetime.datetime.utcfromtimestamp(time)
    day = weekData.weekday() * 60 * 60 * 24
    hour = weekData.hour * 60 * 60
    minute = weekData.minute * 60
    second = weekData.second
    return time - day - hour - minute - second

def getUtcStartOfDay(time):
    weekData = datetime.datetime.utcfromtimestamp(time)
    hour = weekData.hour * 60 * 60
    minute = weekData.minute * 60
    second = weekData.second
    return time - hour - minute - second

def saveJson(filepath, data):
    with io.open(filepath, 'w+', encoding='utf-8') as f:
        f.write(unicode(json.dumps(data, ensure_ascii=False, indent=4, separators=(',', ': '))))

def saveNotices(path, notices):
    oldNotices = json.loads(open(path, 'r').read())['notices']
    for oldNotice in oldNotices:
        for notice in notices:
            if oldNotice['unique_notice_id'] == notice['unique_notice_id']:
                oldNotice.update(notice)
    saveJson(path, {'notices': notices})