#!/usr/bin/python
import sys
import xmpp
#import MySQLdb
import string
import random
import time
import xmlrpclib

def presenceCB(conn,pres):
    froms = str(pres.getFrom())
    patient = froms.split('/')[0]
    status = str(pres.getType())
    presshow = str(pres.getShow())
    
def messageCB(conn,mess):
    text=mess.getBody()
    user=mess.getFrom()
    fulluser = user
    fulluser = str(fulluser)
    text = str(text)
    user = str(user)
    user = user.split("/")
    name = str(user[0])
    if (text  == 'None'):
        response = "nothing to say"
    else :
        response = s.chat(text, fulluser)
        conn.send(xmpp.protocol.Message(name,response))
    print "USER: "+name+" SAID: "+text+"  RESPONSE: "+response
   
for i in globals().keys():
    if i[-7:]=='Handler' and i[:-7].lower()==i[:-7]: commands[i[:-7]]=globals()[i]
    
def StepOn(conn):
    try:
        conn.Process(1)
    except KeyboardInterrupt: return 0
    return 1

def GoOn(conn):
    while StepOn(conn): pass

if len(sys.argv)<3:
    print "Usage: bot.py username@server.net password"
else:
    jid=xmpp.JID(sys.argv[1])
    user,server,password=jid.getNode(),jid.getDomain(),sys.argv[2]

    conn=xmpp.Client(server,debug=[])
    conres=conn.connect() #proxy={'host':'192.102.9.82','port':'3128','user':'pscott','password':'hello1234'})
    s = xmlrpclib.Server('http://localhost:8000')
    if not conres:
        print "Unable to connect to server %s!"%server
        sys.exit(1)
    if conres<>'tls':
        print "Warning: unable to estabilish secure connection - TLS failed!"
    authres=conn.auth(user,password)
    if not authres:
        print "Unable to authorize on %s - check login/password."%server
        sys.exit(1)
    if authres<>'sasl':
        print "Warning: unable to perform SASL auth os %s. Old authentication method used!"%server
    #print "Registering message handler"
    #conn.RegisterHandler('message',messageCB)
    
    conn.RegisterHandler('message',messageCB)
    #conn.RegisterHandler('presence',presenceCB)
	
    conn.sendInitPresence()
    #print "Bot started."
    GoOn(conn)
