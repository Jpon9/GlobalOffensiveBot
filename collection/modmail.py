import praw,pymongo,sys
from pymongo import MongoClient

client = MongoClient('192.168.0.115',27017)
db = client.modmail
collection = db.mail

r = praw.Reddit("DeliriumTremensTest")
r.login("DeliriumTremens", "7hzV2y^$U^q%")

subreddit = r.get_subreddit("globaloffensive")

modmail = r.get_mod_mail(subreddit,limit=1000)
count = 0
after = ''

for mail in modmail:
        insertmail = collection.insert_one({'_id':mail.id,'utc':mail.created_utc,'user':str(mail.author),'subject':mail.subject,'body':mail.body,'replies':[]})
        count+=1
        after = mail.id
        for reply in mail.replies:
                collection.update_one({'_id':mail.id}, {'$push' : {'replies': {'_id':reply.id,'utc':reply.created_utc,'user':str(reply.author),'subject':reply.subject,'body':reply.body}}}, up$
        sys.stdout.write(" Modmails found: %d...    \r" % (count))

while True:
        modmail = r.get_mod_mail(subreddit,params={'after':'t4_'+after},limit=1000)
        for mail in modmail:
                insertmail = collection.insert_one({'_id':mail.id,'user':str(mail.author),'subject':mail.subject,'body':mail.body})
                count+=1
                after = mail.id
                for reply in mail.replies:
                        collection.update_one({'_id':mail.id}, {'$push': {'replies': {'_id':reply.id,'utc':reply.created_utc,'user':str(reply.author),'subject':reply.subject,'body':reply.body$
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
        if count % 1000 > 0:
                sys.stdout.write(" Modmails found: %d...    \r" % (count))
                break
