<<<<<<< HEAD
import os, sys
from PIL import Image
import urllib2
import cStringIO

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Our sprites by default are 45 x 30
def GenerateSpritesheet(imageURLs, width=45, height=30):
	images = []
	for url in imageURLs:
		print url
		filename = cStringIO.StringIO(urllib2.urlopen(url).read())
		images.append(Image.open(filename))
	
	for image in images:
		size = image.size
		if size[0] != width or size[1] != height:
			image = image.thumbnail((width, height), Image.ANTIALIAS)
	 
	spritesheet = Image.new(
	    mode='RGBA',
	    size=(width * len(images), height),
	    color=(0,0,0,1))
	 
	for i, image in enumerate(images):
	    location = width * i
	    spritesheet.paste(image, (location, (height - image.size[1]) / 2))
	path = base_path + "images/spritesheet.jpg"
	spritesheet.save(path, transparency=0)
=======
import os, sys
from PIL import Image
import urllib2
import cStringIO

base_path = os.path.dirname(os.path.abspath(sys.argv[0])) + "/"

# Our sprites by default are 45 x 30
def GenerateSpritesheet(imageURLs, width=45, height=30):
	images = []
	for url in imageURLs:
		filename = cStringIO.StringIO(urllib2.urlopen(url).read())
		images.append(Image.open(filename))
	
	for image in images:
		size = image.size
		if size[0] != width or size[1] != height:
			image = image.thumbnail((width, height), Image.ANTIALIAS)
	 
	spritesheet = Image.new(
	    mode='RGBA',
	    size=(width * len(images), height),
	    color=(0,0,0,1))
	 
	for i, image in enumerate(images):
	    location = width * i
	    spritesheet.paste(image, (location, (height - image.size[1]) / 2))
	path = base_path + "images/spritesheet.jpg"
	spritesheet.save(path, transparency=0)
>>>>>>> 9cac3ffc7ca44891b14144e22e1df4c1b1e14243
	return path