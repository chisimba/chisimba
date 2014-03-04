
import fileinput
import time
import base64
import xmlrpclib
import os
import random
import sys
import zipfile
import os.path

#Define some variables (Change these to suit your circumstances!)
SERV = 'http://127.0.0.1/'
UNAME = 'admin'
PWORD = 'a'
APIENDPOINT = '/app/index.php?module=api'
# -- END EDITABLE REGION -- Please do not attempt to edit below this line, you will break the system! 

def unzip_file_into_dir(file, dir):
    if not os.path.exists(dir):
        os.mkdir(dir, 0777)
    zfobj = zipfile.ZipFile(file)
    for name in zfobj.namelist():
        outfile = open(os.path.join(dir, name), 'wb')
        outfile.write(zfobj.read(name))
        outfile.close()

def grabFiles():
    filestoget = ["allCountries.zip", "alternateNames.zip", "userTags.zip", "admin1Codes.txt", "admin1CodesASCII.txt", "admin2Codes.txt", "countryInfo.txt", "featureCodes_en.txt", "iso-languagecodes.txt", "timeZones.txt"]
    unzippables = ["allCountries.zip", "alternateNames.zip", "userTags.zip"]
    # Get all the files first
    for item in filestoget:
        print "Downloading: "+item
        os.system("wget http://download.geonames.org/export/dump/"+item)
    print "Downloading triples"
    os.system("wget http://download.geonames.org/all-geonames-rdf.zip")
    print "All files downloaded!... Processing..."
    # Unzip the zipballs
    for item in unzippables:
        print "Unzipping "+item
        unzip_file_into_dir(item, '.')
    unzip_file_into_dir("all-geonames-rdf.zip", '.') 
    print "Done! Uploading data to server..."

def doRDFRPC(line): 
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = encoded = base64.b64encode(line)
        result = server.geordf.accept(encoded)
        print result
    except:
        print "RPC FAILED"
        sys.exit()


def doCountryRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = encoded = base64.b64encode(line)
        result = server.geordf.loaddata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()

def doAdmin1CodesRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = encoded = base64.b64encode(line)
        result = server.geordf.loadAdmin1data(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doAdmin1AsciiRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = encoded = base64.b64encode(line)
        result = server.geordf.loadAdmin1Asciidata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doAdmin2CodesRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = encoded = base64.b64encode(line)
        result = server.geordf.loadAdmin2data(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doAltnamesRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadAltnamesdata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()

def doCountryInfoRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadCountryInfodata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doFeatureCodeRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadFeatureCodedata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doIsoLangCodeRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadIsoLangCodedata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doTimeZoneRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadTimeZonedata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()
        
def doUserTagsRPC(line):
    server_url = SERV+APIENDPOINT;
    # Set up the server.
    server = xmlrpclib.Server(server_url);
    try:
        encoded = base64.b64encode(line)
        result = server.geordf.loadUserTagsdata(encoded,UNAME, PWORD)
        return result
    except:
        print "RPC FAILED"
        sys.exit()

def main():
    grabFiles()
    count = 0           
    for line in fileinput.input(['allCountries.txt']):
        count = count+1
        print doCountryRPC(line)+": "+str(count)
   
    print "Country data upload complete!"

    #Now the admin1Codes
    print "Starting to upload first level Admin codes..."
    count = 0
    for line in fileinput.input(['admin1Codes.txt']):
        count = count+1
        print doAdmin1CodesRPC(line)+": "+str(count)
    print "First level Admin codes uploaded!"

    #Now the admin1 ASCII Codes
    print "Starting to upload first level Admin ASCII codes..."
    count = 0
    for line in fileinput.input(['admin1CodesASCII.txt']):
        count = count+1
        print doAdmin1AsciiRPC(line)+": "+str(count)
    print "First level Admin ASCII codes uploaded!"

    #Now the admin2Codes
    print "Starting to upload second level Admin codes..."
    count = 0
    for line in fileinput.input(['admin2Codes.txt']):
        count = count+1
        print doAdmin2CodesRPC(line)+": "+str(count)
    print "Second level Admin codes uploaded!"

    #Now the alternate place names
    print "Starting to upload alternate place names..."
    count = 0
    for line in fileinput.input(['alternateNames.txt']):
        count = count+1
        print doAltnamesRPC(line)+": "+str(count)
    print "Alternate place names uploaded!"

    #Now the Country info
    print "Starting to upload country info..."
    count = 0
    for line in fileinput.input(['countryInfo.txt']):
        count = count+1
        print doCountryInfoRPC(line)+": "+str(count)
    print "Country info uploaded!"

   #Now the Feature codes
    print "Starting to upload feature codes..."
    count = 0
    for line in fileinput.input(['featureCodes_en.txt']):
        count = count+1
        print doFeatureCodeRPC(line)+": "+str(count)
    print "Feature codes uploaded!"

    #Now the ISO Language codes
    print "Starting to upload ISO language codes..."
    count = 0
    for line in fileinput.input(['iso-languagecodes.txt']):
        count = count+1
        print doIsoLangCodeRPC(line)+": "+str(count)
    print "ISO language codes uploaded!"

    #Now the timezones
    print "Starting to upload time zone information..."
    count = 0
    for line in fileinput.input(['timeZones.txt']):
        count = count+1
        print doTimeZoneRPC(line)+": "+str(count)
    print "Time zones uploaded!"

    #Now the user tags
    print "Starting to upload user tags..."
    count = 0
    for line in fileinput.input(['userTags.txt']):
        count = count+1
        print doUserTagsRPC(line)+": "+str(count)
    print "User tags uploaded!"
    
    #Lets finally do the RDF triples before we Geo-rize the whole database
    print "Uploading Linked data..."
    count = 0
    for line in fileinput.input(['all-geonames-rdf.txt']):
        count = count+1
        print doRDFRPC(line)+": "+str(count)
    print "RDF Triples uploaded!"

print "Complete!"
if __name__ == '__main__': main()
