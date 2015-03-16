import threading
import sys
import traceback
import os
import logging

#Logging to file, currently on debug it logs everything, set level=logging.ERROR when everything is stable.
logging.basicConfig(filename='logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	

from reddit import *
from autoposter import autoposterfunc
from flair_dump import FlairDump

threading.Thread(target=main).start()
time.sleep(5)
threading.Thread(target=autoposterfunc).start()
FlairDump()
