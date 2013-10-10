Sun Jan 11 09:54:46 EST 2009

Welcome to the 0.001 version of the SimpleLTI Tool Producer
Framework written in PHP.

It is pretty early and insecure 
  - All passwords are secret
  - It accepts any organization
  - It does not invalidate nonces
  - It tolerates very old launch dates

All this is to make testing practical during development.
You need to improve this to consider use in production.
All of the above need to be policies that make sense
for your application.

In a later version code will be added to accept some option
settings to handle the above choices - for now this is demo
and development.

Steps to make this run.

(1) Make a database in MySql called "lti" - look in 
    lti/DATAMODEL.sql for details

(2) Create all the tables in the lti/DATAMODEL.sql file

(3) Copy lti/db-template.php to db.php and put in the right data

(4) Go to the directory on your server.  I use:

    http://localhost/~csev/php-framework/

    It will say "not properly launched" because
    index.php  really expects to be getting POST data 
    from a launch request from an LMS.
    
(6) There is a fake LMS in the file lms.htm
    Open it up and press submit.  It posts back to 
    index.php - voila you are in the tool.  
    It dumps out all of the LTI provisioning data.

The "launchhtml" launch is for debugging.  Instead of 
returninf the web service response ("launchresolve"),
it dumps out the log data and gives you a link to the
URL it would have returned in the web service.  If
you look a this URL - it will look as follows:

http://localhost/~csev/php-framework/index.php?id=1&password=571beff5e

There are two steps - first the launch is resolved.  This
checks passwords makes all kinds of data, and builds an
LTI session.  Then the above URL is returned to the LMS
in an XML message which puts the URL in an iFrame.

By using launchhtml - you can stop the process and watch 
in slow motion.

If you use launchresolve - you will see XML when you view source:

<launchResponse>
   <status>success</status>
   <type>iframe</type>
   <launchUrl>?id=1&amp;password=60e74037d20a8ef583dc55538ef5e639</launchUrl>
   <launchdebug>
     ....  A LOT OF STUFF
   </launchdebug>
</launchResponse>

If you use "direct" - the launch code will instantly redirect to the 
url with id/password.  This is because some LMS's will actually generate
code in a browser that builds the form and auto submits the launch, 
expecting to be redirected with to the tool after launch is done.

So when the framework sees "direct", after a successful launch setup 
- it just does the redirect.  So if you select "direct" and press Submit,
the next thing yu see is the tool.

The file index.php is the prototype tool.  Take out the debug
and "do your thing".

One thing that might freak you out is that I use a simple Object-
Relational-Mapper of my own making.  At some level it is not essential
for you to use this - the tables are really simple. Butmy ORM handles
the undane bits of writing simple SQL.  Here is the web site for 
my little ORM:

   http://www.omg-software.com/

Again, don't worry too much about this - it just makes my life a lot
easier.  I really hate making SQL strings when there are 15 
string fields.  You can do your own thing.

-- Charles Severance

