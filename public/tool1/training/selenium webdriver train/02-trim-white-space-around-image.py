from PIL import Image, ImageDraw, ImageOps
fileIn = 'fa2.png'
fileOut = 'fa21.png'
##########################
# Open input image
im = Image.open(fileIn)
im = im.convert('RGB')
width, height = im.size   # Get dimensions
im = im.crop((0, 0, width-200, height))

# Get rid of existing black border by flood-filling with white from top-left corner
ImageDraw.floodfill(im,xy=(0,0),value=(255,255,255),thresh=10)
# Get bounding box of text and trim to it
bbox = ImageOps.invert(im).getbbox()
im = im.crop(bbox)

#######################
# 231,233,235
# Add new white border, then new black, then new white border
# res = ImageOps.expand(trimmed, border=10, fill=(255,255,255))
# res = ImageOps.expand(res, border=0, fill=(0,0,0))
# res = ImageOps.expand(res, border=0, fill=(255,255,255))
res = ImageOps.expand(im, border=10, fill=(255,255,255))
res.save(fileOut)

