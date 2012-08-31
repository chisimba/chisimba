<?php

echo '<h1>A demonstration of the tabcontent class</h1>';

$objTabContent = $this->newObject('tabcontent');

$content = '<h3>LDAP Overview</h3>
<p>
LDAP stands for <i>Lightweight Directory Access Protocol</i> and is a widely used tool for providing directory access to clients. Common implementations
can be seen in directory services such as Yahoo!, Netscape, Bigfoot, and many others. The RFC for LDAP v3 is <a href="http://www.five-ten-sg.com/rfc/22/rfc2251.txt" target="_noblank">RFC 2251.</a>

</p><p>
LDAP is modeled after the larger X.500 DAP and was originally designed to be a gateway to X.500 DAP servers. X.500 is an enormous topic in itself and
won\'t be covered here beyond what I\'ve already said. LDAP is <em>client-server</em> in nature. An LDAP client (e.g. Netscape) contacts an LDAP server
with a query and the server responds with a result set based on the query.
</p>';

$content2 = '<p>
Information stored in an LDAP entry can include names, e-mail addresses, phone numbers, and even encrypted passwords for authentication. Entries are 
defined within special files called <i>schemas</i>. There are many schemas available for use in OpenLDAP, and they can even be extended with custom 
attribute types and object classes.
</p><p>
Why would you want to run an LDAP server? Many businesses depend on having managed, centralized directories of employees, customers, and vendors. If
you are responsible for managing these resources, OpenLDAP is a powerful and inexpensive way to achieve this. It is also a popular way to incorporate
user authentication. In this day and age of home networking, the ease of setting up an LDAP server makes creating centralized e-mail directories at
home a cinch. So the question becomes, why <i>wouldn\'t</i> you want to run an LDAP server? ;^)
</p>';

$objTabContent->addTab('First Tab', $content);
$objTabContent->addTab('Second Tab', $content2);
$objTabContent->addTab('UWC Website', '', 'http://www.uwc.ac.za');

$objTabContent->width = '90%';

echo $objTabContent->show();




?>