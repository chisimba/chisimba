#!/bin/bash 
# Try to autodetect OOFFICE and OOOPYTHON. 
OOFFICE=`ls /usr/bin/openoffice /usr/bin/ooffice /usr/lib/openoffice/program/soffice | head -n 1` 
OOOPYTHON=`ls /usr/lib/openoffice/program/python /usr/bin/python | head -n 1` 

if [ ! -x "$OOFFICE" ] 
then echo "Could not auto-detect OpenOffice.org binary" 
exit 
fi 

if [ ! -x "$OOOPYTHON" ] 
then echo "Could not auto-detect OpenOffice.org Python" 
exit 
fi 

echo "Detected OpenOffice.org binary: $OOFFICE" 
echo "Detected OpenOffice.org python: $OOOPYTHON" 

# Reference: http://wiki.services.openoffice.org/wiki/Using_Python_on_Linux 
# If you use the OpenOffice.org that comes with Fedora or Ubuntu, uncomment the following line: 
export PYTHONPATH="/usr/lib/openoffice/program" 

# If you want to simulate for testing that there is no X server, uncomment the next line. 
#unset DISPLAY 

# Kill any running OpenOffice.org processes. 
killall -u `whoami` -q soffice 

# Start OpenOffice.org in listening mode on TCP port 8100. 
$OOFFICE "-accept=socket,host=localhost,port=8100;urp;StarOffice.ServiceManager" -norestore -nofirststartwizard -nologo -headless & 

# Wait a few seconds to be sure it has started. 
sleep 5s 

