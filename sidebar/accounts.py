<<<<<<< HEAD
import json
import os, sys

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"
accounts = json.loads(open(base_path + "config/accounts.json", 'r').read())['accounts']

# Not in use currently since the accounts file is currently static
def refreshAccounts():
	global accounts
	accounts = json.loads(open(base_path + "config/accounts.json", 'r').read())['accounts']

def getAccounts():
=======
import json
import os, sys

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"
accounts = json.loads(open(base_path + "config/accounts.json", 'r').read())['accounts']

# Not in use currently since the accounts file is currently static
def refreshAccounts():
	global accounts
	accounts = json.loads(open(base_path + "config/accounts.json", 'r').read())['accounts']

def getAccounts():
>>>>>>> 9cac3ffc7ca44891b14144e22e1df4c1b1e14243
	return accounts