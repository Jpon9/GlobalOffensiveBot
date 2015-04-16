#!/usr/local/bin/python3.4

import praw,pymongo,sys
from pymongo import MongoClient

client = MongoClient('ip.of.mongo.server',27017)
db = client.modmail
collection = db.mail

r = praw.Reddit("useragent")
r.login("username", "password")

subreddit = r.get_subreddit("subreddit")

modmail = r.get_mod_mail(subreddit,limit=1000)
count = 0
after = ''

for mail in modmail:
        insertmail = collection.insert_one({'_id':mail.id,'user':str(mail.author),'subject':mail.subject,'body':mail.body})
        count+=1
        after = mail.id
        sys.stdout.write(" Modmails found: %d...    \r" % (count))

while True:
        modmail = r.get_mod_mail(subreddit,params={'after':'t4_'+after},limit=1000)
        for mail in modmail:
                insertmail = collection.insert_one({'_id':mail.id,'user':str(mail.author),'subject':mail.subject,'body':mail.body})
                count+=1
                after = mail.id
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
        if count % 1000 > 0:
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
                break