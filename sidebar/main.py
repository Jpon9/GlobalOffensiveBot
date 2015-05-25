#!/usr/bin/env python

import threading
import sys
import traceback
import os, sys
import logging

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

#Logging to file, currently on debug it logs everything, set level=logging.ERROR when everything is stable.
logging.basicConfig(filename=base_path + 'logfile.log', level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%B %d, %Y at %I:%M:%S %p -')	

from reddit import *
from autoposter import autoposter
from flair_dump import FlairDump

threading.Thread(target=main).start()
time.sleep(5)
threading.Thread(target=autoposter).start()
#FlairDump()
