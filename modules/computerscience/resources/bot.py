#!/usr/bin/python

import aiml
from SimpleXMLRPCServer import SimpleXMLRPCServer
import os

server = SimpleXMLRPCServer(("localhost", 8000))
server.register_introspection_functions()

# The Kernel object is the public interface to
# the AIML interpreter.
k = aiml.Kernel()
if os.path.isfile("standard.brn"):
    k.bootstrap(brainFile = "standard.brn")
else:
    k.bootstrap(learnFiles = "std-startup.xml", commands = "load aiml b")
    k.saveBrain("standard.brn")

# Give the asshole a name
k.setBotPredicate("name", "CS4fn")

# Use the 'learn' method to load the contents
# of an AIML file into the Kernel.
#k.learn("std-startup.xml")

#k.learn('<aiml version="1.0.1" xmlns:aiml="http://alicebot.org/2001/AIML-1.0.1"><category><pattern>JOU MA SE POES</pattern><template>Fok jou</template></category></aiml>')
# Use the 'respond' method to compute the response
# to a user's input string.  respond() returns
# the interpreter's response, which in this case
# we ignore.
#k.respond("load aiml b")

def chat(input, user):
    return k.respond(input, user)
    
server.register_function(chat)

# Run the server's main loop
server.serve_forever()

# Loop forever, reading user input from the command
# line and printing responses.
#while True: 
 #   print k.respond(raw_input("> "))
