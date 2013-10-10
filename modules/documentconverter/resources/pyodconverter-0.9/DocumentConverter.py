#
# PyODConverter (Python OpenDocument Converter) v0.9 - 2007-04-05
#
# This script converts a document from one office format to another by
# connecting to an OpenOffice.org instance via Python-UNO bridge.
#
# Copyright (C) 2007 Mirko Nasato <mirko@artofsolving.com>
# Licensed under the GNU LGPL v2.1 - http://www.gnu.org/licenses/lgpl.html
#
DEFAULT_OPENOFFICE_PORT = 8100

import uno
from os.path import abspath, splitext
from com.sun.star.beans import PropertyValue
from com.sun.star.connection import NoConnectException

FAMILY_PRESENTATION = "Presentation"
FAMILY_SPREADSHEET = "Spreadsheet"
FAMILY_TEXT = "Text"

FAMILY_BY_EXTENSION = {
   "odt": FAMILY_TEXT,
   "sxw": FAMILY_TEXT,
   "doc": FAMILY_TEXT,
   "rtf": FAMILY_TEXT,
   "txt": FAMILY_TEXT,
   "wpd": FAMILY_TEXT,
   "html": FAMILY_TEXT,
   "ods": FAMILY_SPREADSHEET,
   "sxc": FAMILY_SPREADSHEET,
   "xls": FAMILY_SPREADSHEET,
   "odp": FAMILY_PRESENTATION,
   "sxi": FAMILY_PRESENTATION,
   "ppt": FAMILY_PRESENTATION
}

FILTER_BY_EXTENSION = {
    "pdf": {
        FAMILY_TEXT: "writer_pdf_Export",
        FAMILY_SPREADSHEET: "calc_pdf_Export",
        FAMILY_PRESENTATION: "impress_pdf_Export"
    },
    "html": {
        FAMILY_TEXT: "HTML (StarWriter)",
        FAMILY_SPREADSHEET: "HTML (StarCalc)",
        FAMILY_PRESENTATION: "impress_html_Export"
    },
    "odt": { FAMILY_TEXT: "writer8" },
    "doc": { FAMILY_TEXT: "MS Word 97" },
    "rtf": { FAMILY_TEXT: "Rich Text Format" },
    "txt": { FAMILY_TEXT: "Text" },
    "ods": { FAMILY_SPREADSHEET: "calc8" },
    "xls": { FAMILY_SPREADSHEET: "MS Excel 97" },
    "odp": { FAMILY_PRESENTATION: "impress8" },
    "ppt": { FAMILY_PRESENTATION: "MS PowerPoint 97" },
    "swf": { FAMILY_PRESENTATION: "impress_flash_Export" }
}


class DocumentConversionException(Exception):

    def __init__(self, message):
        self.message = message

    def __str__(self):
        return self.message


def _unoProps(**args):
    props = []
    for key in args:
        prop = PropertyValue()
        prop.Name = key
        prop.Value = args[key]
        props.append(prop)
    return tuple(props)


class DocumentConverter:
    
    def __init__(self, port=DEFAULT_OPENOFFICE_PORT):
        localContext = uno.getComponentContext()
        resolver = localContext.ServiceManager.createInstanceWithContext("com.sun.star.bridge.UnoUrlResolver", localContext)
        try:
            context = resolver.resolve("uno:socket,host=localhost,port=%s;urp;StarOffice.ComponentContext" % port)
        except NoConnectException:
            raise DocumentConversionException, "failed to connect to OpenOffice.org on port %s" % port
        self.desktop = context.ServiceManager.createInstanceWithContext("com.sun.star.frame.Desktop", context)

    def convert(self, inputFile, outputFile):
        inputExt = self._fileExt(inputFile)
        outputExt = self._fileExt(outputFile)

        filterName = self._filterName(inputExt, outputExt)

        inputUrl = self._fileUrl(argv[1])
        outputUrl = self._fileUrl(argv[2])
        
        document = self.desktop.loadComponentFromURL(inputUrl, "_blank", 0, _unoProps(Hidden=True, ReadOnly=True))
        document.storeToURL(outputUrl, _unoProps(FilterName=filterName))
        document.close(True)

    def _filterName(self, inputExt, outputExt):
        try:
            family = FAMILY_BY_EXTENSION[inputExt]
        except KeyError:
            raise DocumentConversionException, "unknown input format: '%s'" % inputExt
        try:
            filterByFamily = FILTER_BY_EXTENSION[outputExt]
        except KeyError:
            raise DocumentConversionException, "unknown output format: '%s'" % outputExt
        try:
            return filterByFamily[family]
        except KeyError:
            raise DocumentConversionException, "unsupported conversion: from '%s' to '%s'" % (inputExt, outputExt)

    def _fileExt(self, path):
        ext = splitext(path)[1]
        if ext is not None:
            return ext[1:].lower()
    
    def _fileUrl(self, path):
        return uno.systemPathToFileUrl(abspath(path))


if __name__ == "__main__":
    from sys import argv, exit
    
    if len(argv) < 3:
        print "USAGE: " + argv[0] + " <input-file> <output-file>"
        exit(255)

    try:
        converter = DocumentConverter()    
        converter.convert(argv[1], argv[2])
    except DocumentConversionException, exception:
        print "ERROR! " + str(exception)
        exit(1)

