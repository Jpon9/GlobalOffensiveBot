import ts3

server = ts3.TS3Server('ts.redditcsgo.com', 10011)
server.login('Jpon9', 'q2rbf9D7')

# choose virtual server
server.use(1)

# Get the number of connected clients
def GetNumOfVoipUsers():
	return len(server.clientlist()) - 1