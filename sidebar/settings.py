import json
import os

base_path = os.getcwd() + "/"
settings = json.loads(open(base_path + "config/settings.json", 'r').read())['settings']

def refreshSettings():
	global settings
	settings = json.loads(open(base_path + "config/settings.json", 'r').read())['settings']

def getSettings():
	return settings