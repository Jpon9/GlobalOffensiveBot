var Session = require('slackr-bot');

console.log('Creating new session...');
var session = new Session({
	token: 'xoxb-3981963527-efoQLFIhQ5dieo855Yyb0Hps',
	webhookClient: {
		token: 'xoxb-3981963527-efoQLFIhQ5dieo855Yyb0Hps',
		team: 'globaloffensive',
		username: 'globaloffensivebot',
		icon_url: 'https://s3-us-west-2.amazonaws.com/slack-files2/avatars/2015-03-08/3969908713_0f5b9ad8aa5d9b5df970_192.jpg'
	}
});
console.log('Session created.');

session.on('.poll', function(message, match) {
	console.log('Message received!');
	message.reply('I ain\'t no slave');
});

session.on('*', function(message, match) {
	console.log(message.data);
	//message.reply(message.data.text);
});