<?php
class redditConfig{
    //standard, oauth token fetch, and api request endpoints
    static $ENDPOINT_STANDARD = 'http://www.reddit.com';
    static $ENDPOINT_OAUTH = 'https://oauth.reddit.com';
    static $ENDPOINT_OAUTH_AUTHORIZE = 'https://ssl.reddit.com/api/v1/authorize';
    static $ENDPOINT_OAUTH_TOKEN = 'https://ssl.reddit.com/api/v1/access_token';
    static $ENDPOINT_OAUTH_KILL_TOKEN = 'https://ssl.reddit.com/api/v1/revoke_token';
    static $ENDPOINT_OAUTH_REDIRECT = 'http://localhost/login/';
    
    //access token configuration from https://ssl.reddit.com/prefs/apps
    static $CLIENT_ID = '5Aw3pt85Dx9BYA';
    static $CLIENT_SECRET = 'U309p9tavmZ2iUsneYXu6yCdDmo';
    
    //access token request scopes
    //full list at http://www.reddit.com/dev/api/oauth
    static $SCOPES = 'identity,flair,modconfig,modflair,modlog,modposts,modwiki,mysubreddits,read,report,submit,wikiedit,wikiread';
}
?>
