import praw,pymongo,sys
from pymongo import MongoClient
from datetime import datetime

client = MongoClient('host',27017)
db = client.modmail
collection = db.mail

r = praw.Reddit("useragent")
r.login("username", "password")

subreddit = r.get_subreddit("subreddit")
modmail = r.get_mod_mail(subreddit,limit=1000)

count = 0
after = ''

for mail in modmail:
        insertmail = collection.update_one({'_id':mail.id},{
                '$set': {
                        'utc':mail.created_utc,
                        'time':datetime.datetime.fromtimestamp(mail.created_utc).isoformat(),
                        'user':str(mail.author),
                        'subject':mail.subject,
                        'body':mail.body,
                        'replies':[]
                        }
                }, upsert=True)
        count+=1
        after = mail.id
        for reply in mail.replies:
                collection.update_one({
                        '_id':mail.id}, {
                                '$push' : {
                                        'replies': {
                                                '_id':reply.id,
                                                'utc':reply.created_utc,
                                                'time':datetime.datetime.fromtimestamp(reply.created_utc).isoformat(),
                                                'user':str(reply.author),
                                                'subject':reply.subject,
                                                'body':reply.body
                                        }
                                }
                        }, upsert=True)
        sys.stdout.write(" Modmails found: %d...    \r" % (count))

while True:
        modmail = r.get_mod_mail(subreddit,params={'after':'t4_'+after},limit=1000)
        for mail in modmail:
                insertmail = collection.update_one({'_id':mail.id},{
                        '$set': {
                                'utc':mail.created_utc,
                                'time':datetime.datetime.fromtimestamp(mail.created_utc).isoformat(),
                                'user':str(mail.author),
                                'subject':mail.subject,
                                'body':mail.body,
                                'replies':[]
                                }
                        }, upsert=True)
                count+=1
                after = mail.id
                for reply in mail.replies:
                        collection.update_one({
                                '_id':mail.id}, {
                                        '$push' : {
                                                'replies': {
                                                        '_id':reply.id,
                                                        'utc':reply.created_utc,
                                                        'time':datetime.datetime.fromtimestamp(reply.created_utc).isoformat(),
                                                        'user':str(reply.author),
                                                        'subject':reply.subject,
                                                        'body':reply.body
                                                }
                                        }
                                }, upsert=True)
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
        if count % 1000 > 0:
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
                break