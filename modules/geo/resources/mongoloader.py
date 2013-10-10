import glob
import csv
csv.field_size_limit(1000000000)
from pymongo import Connection, GEO2D

db = Connection().geopoints
db.places.create_index([("loc", GEO2D)])

def unicode_csv_reader(utf8_data, dialect=csv.excel, **kwargs):
    csv_reader = csv.reader(utf8_data, dialect=dialect, **kwargs)
    for row in csv_reader:
        yield [unicode(cell, 'utf-8') for cell in row]

li = glob.glob('*.txt')
#print li
for filename in li:
    print "Doing "+filename
    reader = unicode_csv_reader(open(filename), delimiter="\t", quoting=csv.QUOTE_MINIMAL)
    for row in reader:
        insdict = {"loc": [float(row[5]), float(row[4])], "geonameid": [row[0]], "name": [row[1]], "asciiname": [row[2]], "alternatenames": [row[3]], "latitude": [float(row[4])], "longitude": [float(row[5])], "featureclass": [row[6]], "featurecode": [row[7]], "countrycode": [row[8]], "cc2": [row[9]], "admin1code": [row[10]], "admin2code": [row[11]], "admin3code": [row[12]], "admin4code": [row[13]], "population": [row[14]], "elevation": [row[15]], "gtopo30": [row[16]], "timezone": [row[17]], "modificationdate": [row[18]]}
        db.places.insert(insdict)
    print filename + "Done"

